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

        // Precise semantic keyword pairs — let Google filter irrelevant results for us
        $keywords = [
            '中途 貓',
            '中途 狗',
            '中途 領養',
            '中途 餐廳',
            '領養 浪浪'
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
                        'X-Goog-FieldMask' => 'places.displayName,places.formattedAddress,places.location,places.types,places.websiteUri'
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

                        // 1. Blacklist filter: skip commercial breeding or trading shops
                        $blacklist = ['買賣', '繁殖', '品種', '名犬', '名貓', '出售', '合法犬舍', '專業犬舍', '精品寵物'];
                        foreach ($blacklist as $badWord) {
                            if (str_contains($name, $badWord)) {
                                $this->warn("  - Skipping (Commercial/Breeding): {$name}");
                                continue 2; // Skip this place and continue to the next one
                            }
                        }

                        // Use updateOrCreate to ensure no duplicates based on name and address
                        FosterVenue::updateOrCreate(
                            [
                                'name' => $name,
                                'address' => $address
                            ],
                            [
                                'city' => $county,
                                'latitude' => $place['location']['latitude'] ?? null,
                                'longitude' => $place['location']['longitude'] ?? null,
                                'website_url' => $place['websiteUri'] ?? null,
                                'type' => $this->determineType($name, $place['types'] ?? []),
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
