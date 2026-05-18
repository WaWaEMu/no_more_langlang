<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\FosterVenue;

class FetchFosterVenues extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fetch:foster-venues';

    /**
     * The console command description.
     */
    protected $description = 'Fetch midway merchant data nationwide via Google Places API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = env('GOOGLE_PLACES_API_KEY');

        if (!$apiKey) {
            $this->error('Error: GOOGLE_PLACES_API_KEY is not set in .env');
            return;
        }

        // Full Taiwan counties including offshore islands
        $counties = [
            '台北',
            '新北',
            '基隆',
            '桃園',
            '新竹',
            '苗栗',
            '台中',
            '彰化',
            '南投',
            '雲林',
            '嘉義',
            '台南',
            '高雄',
            '屏東',
            '宜蘭',
            '花蓮',
            '台東',
            '澎湖',
            '金門',
            '馬祖'
        ];

        // Targeted keywords — cast a wide net, whitelist filter handles precision
        $keywords = [
            '中途 認養 OR 送養',
            '浪浪',
            '流浪動物 護生園 狗園',
            '收容所 動物之家 教育園區',
            '美髮 貓 OR 狗',
            '漫畫 貓 OR 狗',
            '咖啡廳 貓 OR 狗',
            '寵物用品 認養 OR 送養 OR 中途',
            '寵物美容 認養 OR 送養 OR 中途',
            '寵物旅館 認養 OR 送養 OR 中途',
            '工作室 認養 OR 送養 OR 中途'
        ];

        // 1. Ineligible Blacklist (Skip immediately to save API tokens and AI fees)
        // Combines global non-animal terms and strict commercial/breeder keywords.
        // We DO NOT block "貓舍/犬舍" here to prevent false positives like "新屋貓舍義工團".
        $blacklist = [
            '移民署', '更生', '戒毒', '少年', '基督教', '更生團契', '法務部',
            '福利', '心理', '安置', '野生', '保育類', '試驗', '研究', '實驗',
            '辦公室', '辦事處', '動保處', '防疫所', '試吃', '婚宴', '喜餅',
            '水世界', '寄養', '佛學會', '醫院',
            '寵物美容學苑', '寵物美容學院',
            '品種', '買賣', '名貓', '名犬', '自家繁殖', '特寵字', '特寵證', '血統', '出售'
        ];

        // 2. Definitive/Official Animal Shelters (100% trusted, passes instantly as verified)
        $definitiveShelters = [
            '狗園', 'TNR', '動物之家', '保育場', '動保', '浪浪',
            '認養中心', '送養中心', '領養中心', '護生園', '教育園區', '收容所',
            '中途之家', '義工團', '愛護動物協會', '保護動物協會'
        ];

        // 3. Official Google Place Type (Passes instantly as verified)
        $officialTypes = [
            'animal_shelter'
        ];

        $url = 'https://places.googleapis.com/v1/places:searchText';

        foreach ($counties as $county) {
            foreach ($keywords as $keyword) {
                $query = "{$county} {$keyword}";
                $this->info("Searching: {$query}...");

                try {
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'X-Goog-Api-Key' => $apiKey,
                        'X-Goog-FieldMask' => implode(',', [
                            'places.displayName',
                            'places.formattedAddress',
                            'places.addressComponents',
                            'places.location',
                            'places.types',
                            'places.primaryTypeDisplayName',
                            'places.websiteUri',
                            'places.nationalPhoneNumber',
                            'places.rating',
                            'places.userRatingCount',
                            'places.regularOpeningHours',
                            'places.businessStatus',
                        ])
                    ])->post($url, [
                                'textQuery' => $query,
                                'languageCode' => 'zh-TW'
                            ]);

                    if (!$response->successful()) {
                        $this->error("Failed to fetch data for {$query}: " . $response->body());
                        continue;
                    }

                    $places = $response->json()['places'] ?? [];

                    foreach ($places as $place) {
                        $name = $place['displayName']['text'] ?? '';
                        $address = $this->fixAddress($place['formattedAddress'] ?? '');
                        $googleTypes = $place['types'] ?? [];

                        // 1. Blacklist Check (Instant Skip)
                        foreach ($blacklist as $badWord) {
                            if (str_contains($name, $badWord)) {
                                $this->warn("  - Skipping (Blacklist Match): {$name}");
                                continue 2;
                            }
                        }

                        // 2. Skip permanently closed businesses
                        $businessStatus = $place['businessStatus'] ?? 'OPERATIONAL';
                        if ($businessStatus === 'CLOSED_PERMANENTLY') {
                            $this->warn("  - Skipping (Permanently Closed): {$name}");
                            continue;
                        }

                        // 3. VIP Whitelist: Identify 100% official/definitive public shelters
                        $isOfficialShelter = !empty(array_intersect($googleTypes, $officialTypes));
                        $isDefinitiveShelter = false;
                        foreach ($definitiveShelters as $kw) {
                            if (str_contains($name, $kw)) {
                                $isDefinitiveShelter = true;
                                break;
                            }
                        }

                        $isVerifiedShelter = $isOfficialShelter || $isDefinitiveShelter;

                        // 4. Send all other non-blacklisted results to AI Agent for verification
                        // No strict keyword matching here to avoid missing quirky names like "毛髦" or "haven hair"

                        $realCity = $this->extractCity($place['addressComponents'] ?? []) ?: $county;

                        // Use firstOrNew to prevent overwriting AI Agent's verification status
                        $venue = FosterVenue::firstOrNew([
                            'name' => $name,
                            'address' => $address
                        ]);

                        // Only set default status and verification for NEW venues
                        if (!$venue->exists) {
                            $venue->status = $isVerifiedShelter ? FosterVenue::STATUS_ACTIVE : FosterVenue::STATUS_PENDING;
                            $venue->is_verified = $isVerifiedShelter;
                        }

                        // Always update basic info, preserving existing status and is_verified
                        $venue->fill([
                            'city' => $this->fixAddress($realCity),
                            'district' => $this->extractDistrict($place['addressComponents'] ?? []),
                            'phone' => $place['nationalPhoneNumber'] ?? null,
                            'latitude' => $place['location']['latitude'] ?? null,
                            'longitude' => $place['location']['longitude'] ?? null,
                            'rating' => $place['rating'] ?? null,
                            'user_rating_count' => $place['userRatingCount'] ?? null,
                            'business_hours' => $place['regularOpeningHours']['weekdayDescriptions'] ?? null,
                            'website_url' => isset($place['websiteUri']) ? substr($place['websiteUri'], 0, 255) : null,
                            'type' => $this->determineType($name, $place['types'] ?? []),
                            'primary_type_display_name' => $place['primaryTypeDisplayName']['text'] ?? null,
                            'business_status' => $businessStatus,
                            'pet_types' => $this->determinePetTypes($name, $keyword),
                            'services' => ['adoption'],
                        ]);

                        $venue->save();

                        $this->line("  - <info>Processed</info>: {$name}");
                    }

                    // Throttling to respect API rate limits and keep it steady
                    usleep(300000); // 0.3 seconds

                } catch (\Exception $e) {
                    $this->error("Error occurred during {$query}: " . $e->getMessage());
                }
            }
        }

        $this->info("\nNationwide midway merchant data collection complete!");
    }

    /**
     * Extract city (縣市, e.g. 臺北市, 新北市) from Google Places addressComponents.
     */
    private function extractCity(array $components): ?string
    {
        foreach ($components as $component) {
            if (in_array('administrative_area_level_1', $component['types'] ?? [])) {
                return $component['longText'] ?? null;
            }
        }

        return null;
    }

    /**
     * Extract district (行政區, e.g. 中正區, 松山區) from Google Places addressComponents.
     * In Taiwan, administrative_area_level_2 = 區/鄉/鎮, which is the district we want.
     */
    private function extractDistrict(array $components): ?string
    {
        foreach ($components as $component) {
            if (in_array('administrative_area_level_2', $component['types'] ?? [])) {
                $district = $component['longText'] ?? null;
                return $district ? $this->fixAddress($district) : null;
            }
        }

        return null;
    }

    /**
     * Determine the venue tags (array) based on name and Google Place types
     */
    private function determineType(string $name, array $googleTypes): array
    {
        $name = mb_strtolower($name);
        $tags = [];

        if (str_contains($name, '髮') || str_contains($name, '沙龍') || str_contains($name, 'salon') || in_array('hair_care', $googleTypes)) {
            $tags[] = FosterVenue::TYPE_HAIR_SALON;
        }
        if (str_contains($name, '動物醫院') || str_contains($name, '獸醫') || in_array('veterinary_care', $googleTypes)) {
            $tags[] = FosterVenue::TYPE_VET_CLINIC;
        }
        if (str_contains($name, '寵物用品') || str_contains($name, '寵物館') || str_contains($name, '水族') || in_array('pet_store', $googleTypes)) {
            $tags[] = FosterVenue::TYPE_PET_SUPPLIES;
        }
        if (str_contains($name, '美容') || str_contains($name, '洗狗') || str_contains($name, '洗貓') || str_contains($name, 'grooming')) {
            $tags[] = FosterVenue::TYPE_PET_GROOMING;
        }
        if (str_contains($name, '旅館') || str_contains($name, '住宿') || str_contains($name, '寄宿') || str_contains($name, 'hotel')) {
            $tags[] = FosterVenue::TYPE_PET_HOTEL;
        }
        if (
            str_contains($name, '餐廳') || str_contains($name, '咖啡') || str_contains($name, 'cafe') || str_contains($name, '食堂') ||
            in_array('restaurant', $googleTypes) || in_array('cafe', $googleTypes) || in_array('food', $googleTypes)
        ) {
            $tags[] = FosterVenue::TYPE_RESTAURANT_CAFE;
        }
        if (str_contains($name, '收容所') || str_contains($name, '動物之家')) {
            $tags[] = FosterVenue::TYPE_PUBLIC_SHELTER;
        }
        if (str_contains($name, '協會') || str_contains($name, '保育場') || str_contains($name, '狗園') || str_contains($name, '中途之家') || str_contains($name, '護生園') || in_array('animal_shelter', $googleTypes)) {
            $tags[] = FosterVenue::TYPE_PRIVATE_SHELTER;
        }
        if (str_contains($name, '工作室') || str_contains($name, 'studio')) {
            $tags[] = FosterVenue::TYPE_STUDIO;
        }
        if (str_contains($name, '書') || str_contains($name, '漫畫') || str_contains($name, '桌遊') || str_contains($name, '空間') || str_contains($name, '藝廊')) {
            $tags[] = FosterVenue::TYPE_LEISURE_HYBRID;
        }

        if (empty($tags)) {
            $tags[] = FosterVenue::TYPE_STUDIO; 
        }

        return array_unique($tags);
    }

    /**
     * Fix common Simplified Chinese characters returned by Google
     */
    private function fixAddress(string $address): string
    {
        // 1. Remove leading postal code and "Taiwan" (Support both 台/臺 and optional space)
        // Matches: "202臺灣", "202 台灣", "202 臺灣", etc.
        $address = preg_replace('/\d{3,5}\s?(台灣|臺灣)/u', '', $address);

        $map = [
            // Administrative divisions
            '区' => '區',
            '县' => '縣',
            '乡' => '鄉',
            '镇' => '鎮',
            '里' => '里',

            // Common Taiwan place name characters
            '龙' => '龍',
            '图' => '圖',
            '湾' => '灣',
            '门' => '門',
            '园' => '園',
            '营' => '營',
            '丰' => '豐',
            '关' => '關',
            '复' => '復',
            '义' => '義',
            '万' => '萬',
            '双' => '雙',
            '庄' => '莊',
            '东' => '東',
            '国' => '國',
            '业' => '業',
            '号' => '號',
            '楼' => '樓',
            '台' => '臺',
        ];

        return str_replace(array_keys($map), array_values($map), $address);
    }


    /**
     * Heuristic to determine pet types based on name or keyword
     */
    private function determinePetTypes(string $name, string $keyword): array
    {
        $types = [];
        $combined = $name . $keyword;

        if (str_contains($combined, '貓') || str_contains($combined, 'cat')) {
            $types[] = 'cat';
        }

        if (str_contains($combined, '狗') || str_contains($combined, 'dog')) {
            $types[] = 'dog';
        }

        return empty($types) ? ['cat'] : array_unique($types);
    }
}
