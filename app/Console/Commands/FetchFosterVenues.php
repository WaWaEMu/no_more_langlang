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
                        $address = $place['formattedAddress'] ?? '';

                        if (empty($name))
                            continue;

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
                                'type' => $this->determineType($place['types'] ?? []),
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
     * Determine the venue type based on Google Place types
     */
    private function determineType(array $googleTypes): string
    {
        if (in_array('restaurant', $googleTypes)) {
            return FosterVenue::TYPE_RESTAURANT;
        }

        if (in_array('veterinary_care', $googleTypes)) {
            return FosterVenue::TYPE_VET_CLINIC;
        }

        return FosterVenue::TYPE_CAFE;
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
