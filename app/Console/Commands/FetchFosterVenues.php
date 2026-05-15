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
            '中途 送養',
            '浪浪 送養',
            '流浪動物 護生園 狗園',
            '收容所 動物之家 教育園區',
        ];

        // 1. Foster-related strong keywords (Needs animal context)
        $fosterKeywords = [
            '中途',
            '領養',
            '認養',
            '送養',
            '收容',
            '流浪',
            '救援',
            '庇護',
            '照護中心',
            '護生',
            '愛媽',
            '愛爸'
        ];

        // 2. Animal-related keywords (The context)
        $animalKeywords = [
            '貓',
            '狗',
            '喵',
            '汪',
            '毛孩',
            '毛小孩',
            '動物',
            '寵物',
            '米克斯',
            '浪犬',
            '浪貓'
        ];

        // 3. Definitive Animal Venue keywords (Passes even without dual check)
        $definitiveAnimalKeywords = [
            '狗園',
            '貓園',
            'TNR',
            '動物之家',
            '保育場',
            '動保',
            '浪浪',
            '認養中心',
            '送養中心',
            '領養中心',
            '護生園',
            '教育園區',
            '躍動園區',
            '寵物銀行',
            '貓格里拉',
            '小犬',
            '浪浪別哭',
            '咪可思',
            '躍動園區',
            '新屋貓舍',
            '狗腳印',
            '好好善待',
            '流浪狗中途之家',
            '外帶一隻貓',
            '和貓咪有約',
            '東森寵物',
            '認養小站',
            'Help Save A Pet',
            '燕巢動物保護關愛園區',
            '狗場',
            '巴克幫',
            '金汪汪',
            '䕶生園',
            '犬山居',
            '愛狗人協會',
            '賴媽媽',
            '毛小孩幸福農莊',
            '善化收容所',
            '莉丰慧民',
            '世界聯合保護動物協會',
            '小松樹',
            '毛小孩的窩仁德園區',
            '貓咪也瘋狂公益協會',
            '貓狗甜園',
            '新竹收容所'
        ];

        // 4. Ineligible Venue Blacklist (Instant skip)
        $globalBlacklist = [
            '移民署',
            '更生',
            '戒毒',
            '少年',
            '基督教',
            '更生團契',
            '法務部',
            '福利',
            '心理',
            '安置',
            '野生',
            '保育類',
            '試驗',
            '研究',
            '實驗',
            '貓舍',
            '犬舍',
            '辦公室',
            '辦事處',
            '動保處',
            '防疫所',
            '試吃',
            '婚宴',
            '喜餅',
            '水世界',
            '寄養',
            '佛學會',
            '醫院'
        ];

        // 5. Specific Precise Blacklist (Names that trick the system)
        $preciseBlacklist = [
            '天使之戀',
            '聖力貓舍',
            '寵翻天寵物家族',
            '庭園寵物',
            '動物保護處',
            '汪喵歡樂營',
            '貓膩會館',
            '寵物公園(苗栗竹南)',
            '觀音的家',
            '屏東動物之家—寵物美容、行為訓練中心',
            '新店動物之家山下辦公室',
            'UNI Cafe'
        ];

        $typeWhitelist = [
            'animal_shelter',
            'non_governmental_organization',
            'pet_store',
            'restaurant',
            'cafe',
            'pet_boarding_service',
            'veterinary_care'
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

                        // 1. Precise Blacklist: ULTIMATE SKIP (No one bypasses this, not even VIPs)
                        foreach ($preciseBlacklist as $badName) {
                            if (str_contains($name, $badName)) {
                                $this->warn("  - Skipping (Precise Blacklist Match): {$name}");
                                continue 2;
                            }
                        }

                        // 2. VIP Whitelist: Identify definitive animal venues
                        $isDefinitiveAnimal = false;
                        foreach ($definitiveAnimalKeywords as $kw) {
                            if (str_contains($name, $kw)) {
                                $isDefinitiveAnimal = true;
                                break;
                            }
                        }

                        // 3. Global Blacklist: Only check if NOT a definitive animal venue
                        if (!$isDefinitiveAnimal) {
                            foreach ($globalBlacklist as $badWord) {
                                if (str_contains($name, $badWord)) {
                                    $this->warn("  - Skipping (Ineligible Venue): {$name}");
                                    continue 2;
                                }
                            }
                        }

                        // 4. Skip permanently closed businesses
                        $businessStatus = $place['businessStatus'] ?? 'OPERATIONAL';
                        if ($businessStatus === 'CLOSED_PERMANENTLY') {
                            $this->warn("  - Skipping (Permanently Closed): {$name}");
                            continue;
                        }

                        // 5. Relevance Verification
                        $isOfficialShelter = !empty(array_intersect($googleTypes, $typeWhitelist));

                        $hasFosterWord = false;
                        foreach ($fosterKeywords as $kw) {
                            if (str_contains($name, $kw)) {
                                $hasFosterWord = true;
                                break;
                            }
                        }

                        $hasAnimalWord = false;
                        foreach ($animalKeywords as $kw) {
                            if (str_contains($name, $kw)) {
                                $hasAnimalWord = true;
                                break;
                            }
                        }

                        $isFosterAnimalVenue = $hasFosterWord && $hasAnimalWord;

                        if (!$isDefinitiveAnimal && !$isOfficialShelter && !$isFosterAnimalVenue) {
                            $this->warn("  - Skipping (Not an animal foster venue): {$name}");
                            continue;
                        }

                        $realCity = $this->extractCity($place['addressComponents'] ?? []) ?: $county;

                        // Use updateOrCreate to ensure no duplicates based on name and address
                        FosterVenue::updateOrCreate(
                            [
                                'name' => $name,
                                'address' => $address
                            ],
                            [
                                'city' => $this->fixAddress($realCity),
                                'district' => $this->extractDistrict($place['addressComponents'] ?? []),
                                'phone' => $place['nationalPhoneNumber'] ?? null,
                                'latitude' => $place['location']['latitude'] ?? null,
                                'longitude' => $place['location']['longitude'] ?? null,
                                'rating' => $place['rating'] ?? null,
                                'user_rating_count' => $place['userRatingCount'] ?? null,
                                'business_hours' => $place['regularOpeningHours']['weekdayDescriptions'] ?? null,
                                'website_url' => $place['websiteUri'] ?? null,
                                'type' => $this->determineType($name, $place['types'] ?? []),
                                'primary_type_display_name' => $place['primaryTypeDisplayName']['text'] ?? null,
                                'business_status' => $businessStatus,
                                'status' => FosterVenue::STATUS_ACTIVE,
                                'pet_types' => $this->determinePetTypes($name, $keyword),
                                'services' => ['adoption'],
                                'is_verified' => false
                            ]
                        );

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
     * Determine the venue type based on name and Google Place types
     */
    private function determineType(string $name, array $googleTypes): string
    {
        $name = mb_strtolower($name);
        // 1. Check by Name Keywords (Priority)
        if (str_contains($name, '髮') || str_contains($name, '沙龍') || str_contains($name, 'salon')) {
            return FosterVenue::TYPE_HAIR_SALON;
        }
        if (str_contains($name, '桌遊') || str_contains($name, '遊戲')) {
            return FosterVenue::TYPE_BOARD_GAME;
        }
        if (str_contains($name, '收容所') || str_contains($name, '動物之家') || str_contains($name, '協會')) {
            return FosterVenue::TYPE_SHELTER;
        }
        // 新增：判定寵物用品店
        if (str_contains($name, '寵物用品') || str_contains($name, '寵物館') || in_array('pet_store', $googleTypes)) {
            return FosterVenue::TYPE_PET_STORE;
        }

        // 2. Check by Google Types
        if (
            in_array('restaurant', $googleTypes) ||
            in_array('food', $googleTypes) ||
            in_array('cafe', $googleTypes) ||
            in_array('coffee_shop', $googleTypes)
        ) {
            return FosterVenue::TYPE_RESTAURANT;
        }

        if (in_array('veterinary_care', $googleTypes)) {
            return FosterVenue::TYPE_VET_CLINIC;
        }

        return FosterVenue::TYPE_OTHER;
    }

    /**
     * Fix common Simplified Chinese characters returned by Google
     */
    private function fixAddress(string $address): string
    {
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
