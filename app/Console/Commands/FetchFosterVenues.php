<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchFosterVenues extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fetch:foster-venues';

    /**
     * The console command description.
     */
    protected $description = 'Fetch midway merchant data via Google Places API';

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

        // Test search with "Taipei" first
        $query = '中途咖啡廳 台北';
        $this->info("Fetching from Google Maps: {$query}...");

        // Google Places API (New) Text Search endpoint
        $url = 'https://places.googleapis.com/v1/places:searchText';

        // Perform API request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Goog-Api-Key' => $apiKey,
            // FieldMask: specify needed fields to optimize payload and cost
            'X-Goog-FieldMask' => 'places.displayName,places.formattedAddress,places.location,places.id,places.types'
        ])->post($url, [
                    'textQuery' => $query,
                    'languageCode' => 'zh-TW'
                ]);

        if ($response->successful()) {
            $data = $response->json();
            $places = $data['places'] ?? [];

            if (empty($places)) {
                $this->warn("No places found.");
                return;
            }

            $this->info("Successfully fetched " . count($places) . " places:");

            foreach ($places as $place) {
                $name = $place['displayName']['text'] ?? 'Unknown Name';
                $address = $place['formattedAddress'] ?? 'Unknown Address';

                $this->line("- <info>{$name}</info>: {$address}");
            }

            $this->info("\nData fetch test successful!");
        } else {
            $this->error("API call failed: " . $response->body());
        }
    }
}
