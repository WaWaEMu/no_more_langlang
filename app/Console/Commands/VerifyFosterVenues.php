<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\FosterVenue;

class VerifyFosterVenues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:verify-venues {--name= : Manually verify and ingest a specific venue by name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AI Agent: Verify pending foster venues by searching the web and analyzing with LLM';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $manualName = $this->option('name');
        if ($manualName) {
            $this->info("🔍 [Manual] Verifying: {$manualName}");

            // Clean the manual name for search: strip out parentheses/brackets and their contents
            $cleanManualName = preg_replace('/(\(|（).*?(\)|）)/u', '', $manualName);
            $cleanManualName = trim($cleanManualName);

            // Crafting the search query with explicit logical grouping
            $searchQuery = "\"{$cleanManualName}\" AND (\"中途\" OR \"認養\" OR \"送養\" OR \"領養\")";
            $this->line("Sending search query: [ {$searchQuery} ]");

            $snippets = $this->performSearch($searchQuery);

            if (empty($snippets)) {
                $this->error("No search results found for {$manualName}.");
                return;
            }

            $this->info("[AI] Snippets retrieved. Analyzing with Gemini...");
            $aiResult = $this->analyzeWithAI($manualName, "", $snippets);

            if ($aiResult) {
                if ($aiResult['is_foster_venue'] ?? false) {
                    $this->info("✅ [Approved] Reason: {$aiResult['reason']}");
                    
                    // Fetch official Google Places details for accurate DB info
                    $this->info("Fetching official details from Google Places API...");
                    $placeDetails = $this->fetchPlaceDetails($manualName);
                    
                    if ($placeDetails) {
                        $this->ingestManualVenue($manualName, $placeDetails);
                    } else {
                        // Create basic model entry if Google Places doesn't return anything
                        $venue = FosterVenue::firstOrNew(['name' => $manualName]);
                        $venue->status = FosterVenue::STATUS_ACTIVE;
                        $venue->is_verified = true;
                        $venue->save();
                        $this->info("[Saved] {$manualName} (Google Places search returned no direct match).");
                    }
                } else {
                    $this->warn("❌ [Rejected] Reason: {$aiResult['reason']}");
                }
            }
            return;
        }

        $this->info("Starting fully automated AI Foster Venue Verification...");

        // Fetch pending venues from the database
        $pendingVenues = FosterVenue::where('status', FosterVenue::STATUS_PENDING)
            ->where('is_verified', false)
            ->get();

        if ($pendingVenues->isEmpty()) {
            $this->info("No pending venues found. Everything is up to date!");
            return;
        }

        $this->info("Found {$pendingVenues->count()} pending venues to verify.");

        foreach ($pendingVenues as $venue) {
            $this->info("\n----------------------------------------");
            $this->info("🔍 Verifying: {$venue->name}");

            $locationContext = trim(($venue->city ?? '') . ' ' . ($venue->district ?? ''));

            // Clean the venue name for search: strip out parentheses/brackets (both English and Full-width Chinese) and their contents
            $cleanName = preg_replace('/(\(|（).*?(\)|）)/u', '', $venue->name);
            $cleanName = trim($cleanName);

            // Crafting the search query with explicit logical grouping and geographic context to differentiate branches
            $searchQuery = "\"{$cleanName}\"";
            if ($locationContext) {
                $searchQuery .= " {$locationContext}";
            }
            $searchQuery .= " AND (\"中途\" OR \"認養\" OR \"送養\" OR \"領養\")";

            $this->line("Sending search query: [ {$searchQuery} ]");

            $snippets = $this->performSearch($searchQuery);

            if (empty($snippets)) {
                $this->warn("⚠️ [Warning] No search results found for {$venue->name}. Skipping for now.");
                continue;
            }

            $this->info("[AI] Snippets retrieved. Analyzing with Gemini...");
            $aiResult = $this->analyzeWithAI($venue->name, $locationContext, $snippets);

            if ($aiResult) {
                if ($aiResult['is_foster_venue'] ?? false) {
                    $this->info("✅ [Approved] Reason: {$aiResult['reason']}");
                    // Update database for approved venues
                    $venue->status = FosterVenue::STATUS_ACTIVE;
                    $venue->is_verified = true;
                } else {
                    $this->warn("❌ [Rejected] Reason: {$aiResult['reason']}");
                    // Update database for rejected venues so we don't check them again
                    $venue->status = FosterVenue::STATUS_CLOSED;
                }

                // Save the AI's decision to the database
                $venue->save();
            } else {
                $this->error("Failed to get a valid response from AI for {$venue->name}.");
            }

            // Add a small delay (1 second) to be polite to Serper and Google Search API limits
            // Since you are on the paid plan, Gemini's RPM limit is now 1,000, allowing near-instant checks!
            sleep(1);
        }

        $this->info("\nVerification process completed.");
    }

    /**
     * Agent's search tool: Fetch Google search snippets via Serper API
     */
    private function performSearch(string $query): string
    {
        $apiKey = env('SERPER_API_KEY');
        if (empty($apiKey)) {
            $this->error('Please ensure SERPER_API_KEY is set in your .env file.');
            return '';
        }

        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Content-Type' => 'application/json'
            ])->post('https://google.serper.dev/search', [
                        'q' => $query,
                        'gl' => 'tw',        // Target Taiwan
                        'hl' => 'zh-tw',     // Use Traditional Chinese
                        'num' => 5           // Fetch top 5 results to save AI context window and tokens
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                $snippets = [];

                // Parse the organic search results from Google
                if (isset($data['organic'])) {
                    foreach ($data['organic'] as $result) {
                        $title = $result['title'] ?? '';
                        $snippet = $result['snippet'] ?? '';
                        $snippets[] = "Title: {$title}\nSnippet: {$snippet}";
                    }
                }

                // Combine the top 5 snippets into a single text block for the AI
                return implode("\n---\n", $snippets);
            } else {
                $this->error("Serper API error response: " . $response->body());
            }
        } catch (\Exception $e) {
            $this->error("Exception occurred during search: " . $e->getMessage());
        }

        return '';
    }

    /**
     * Agent's reasoning tool: Perform LLM logical deduction via Gemini API
     */
    private function analyzeWithAI(string $name, string $locationContext, string $snippets): ?array
    {
        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            $this->error('Please ensure GEMINI_API_KEY is set in your .env file.');
            return null;
        }

        $locationString = $locationContext ? "（位於 {$locationContext}）" : "";

        $prompt = "你是一位台灣流浪動物中途商家的「極度嚴格」審核專家。\n"
            . "任務：請根據以下 Google 搜尋摘要，判斷這家店「{$name}」{$locationString}是否「長期且持續地在店內提供流浪貓狗的中途、安置或認養/送養服務」。\n\n"
            . "【⚠️ 絕對排除條款 - 出現以下情況必須判斷為 false】\n"
            . "1. 「商業寵物買賣/品種犬貓販售」（零容忍）：如果該店家本身有從事「商業品種犬貓買賣、出售、販售、合法/非法繁殖」（例如販售特寵、品種幼犬、幼貓、英短、布偶貓、柴犬等），不論它是否曾參與政府的各種公益/收容所認養合作活動（如台北市動保處 TAS 浪愛滿屋計畫、一日中途店家），都【必須判定為 false】！本平台對商業寵物買賣零容忍，絕對不可將其核准為中途商家！\n"
            . "2. 「純寵物友善 / 僅有店貓店狗」：如果該店只是「寵物友善空間」，或者店內只是養了幾隻「不開放認養的自家店貓/店狗」供客人陪伴（例如：貓圖咖啡等一般貓咪咖啡廳），但並沒有「常駐且持續提供流浪貓狗送養」，則【必須判定為 false】！有店貓絕對不等於是中途之家！\n"
            . "3. 「單次/短期活動」：僅舉辦過單次或短期的流浪動物認養會、義賣會、新書發表會或動保講座。\n"
            . "4. 「僅捐款/義賣」：僅將部分盈餘捐給動保團體，或僅在店內置放發票箱、販售公益年曆，但店內並無實際安置中途浪浪。\n"
            . "5. 「名字誤導」：例如「小獸書屋」雖有「獸」字，但本質是獨立書店，除非有強烈證據顯示它「長期在店內安置流浪貓狗供人認養」，否則一律排除。\n"
            . "6. 「純科技館/觀光景點/娛樂場所」：如 ANIVERSE KEELUNG，即使曾與動保處合作辦過一次性認養活動，也絕非中途商家，必須排除。\n"
            . "7. 「已退役/過時歷史資訊」（極度重要）：台灣許多早期老牌貓咪咖啡廳（如：極簡咖啡館 Minimal Cafe）在十年前曾是知名中途之家，但如今已停止送養，店內全為「已收編的非賣品老店貓」。若搜尋摘要顯示的資訊多為「超過三年以上的舊文/舊網誌」，且缺乏「最近三年內」明確的「現正送養中」、「待認養貓狗」資訊，【必須判定為 false】！本平台只收錄「最近三年內仍有活躍送養紀錄」的中途商家。\n\n"
            . "【⚠️ 嚴格防範「跨主體關聯謬誤」（重要！）】\n"
            . "在搜尋摘要中，不同商家的資訊可能因為「並列推薦」、「懶人包對比清單（例如：推薦10大餐廳，包含中途咖啡廳A、純寵物友善餐廳B）」或「YouTube 頻道系列影片名稱（例如：探訪「{$name}」...與大型貓互動...【轉運棧－貓中途咖啡廳ep1】）」而同時出現在同一個摘要中。\n"
            . "你必須「語意理解」這些詞彙與本商家「{$name}」{$locationString}的真實關係。\n"
            . "- 錯誤範例：若摘要寫著『探訪「{$name}」！...與大型貓互動...【轉運棧－貓中途咖啡廳ep1】』，這代表中途是「轉運棧」，而不是「{$name}」，此時你必須判定為 false。\n"
            . "- 錯誤範例：若摘要是某人抱怨『去貓舍「{$name}」問免費認養...』，這代表它是貓舍，不是中途，此時你必須判定為 false。\n"
            . "- 黃金法則：只有當搜尋摘要中，明確且直接指出「{$name}」這家店本身就是貓咪中途、有提供中途安置或店內貓狗供認養時，才能判定為 true！\n\n"
            . "【🟢 核准標準 - 必須符合以下情況才能為 true】\n"
            . "- 該店「長期且持續地」作為流浪貓狗的避風港/中途站，店內常年有待認養的貓狗活動，並有明確的送養與領養機制。\n\n"
            . "搜尋摘要：\n{$snippets}\n\n"
            . "請務必只回傳純 JSON 格式，不要包含 Markdown 語法（例如 ```json），格式如下：\n"
            . "{\n"
            . '  "is_foster_venue": true 或 false,' . "\n"
            . '  "confidence_score": 0到100的整數,' . "\n"
            . '  "reason": "你的判斷理由（繁體中文，限 50 字以內，若判定為 false 請明確指出違反了哪條排除條款或謬誤條款）"' . "\n"
            . "}";

        $maxRetries = 3;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                // Gemini API endpoint (Using the production-stable gemini-1.5-flash model to avoid experimental preview limits & 503 errors)
                $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json'
                ])->post($url, [
                            'contents' => [
                                [
                                    'parts' => [
                                        ['text' => $prompt]
                                    ]
                                ]
                            ],
                            'generationConfig' => [
                                'temperature' => 0.1, // Keep it calm and objective to reduce hallucinations
                                'responseMimeType' => 'application/json' // Force JSON output
                            ]
                        ]);

                if ($response->successful()) {
                    $result = $response->json();
                    $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

                    // Strip out potential markdown code blocks
                    $text = trim(str_replace(['```json', '```'], '', $text));

                    return json_decode($text, true);
                } elseif (in_array($response->status(), [429, 500, 503])) {
                    $attempt++;
                    $waitTime = 15 * $attempt; // Exponential backoff: 15s, 30s, 45s
                    $this->warn("⚠️ API Overloaded or Rate limited ({$response->status()}). Sleeping for {$waitTime} seconds before retrying (Attempt {$attempt}/{$maxRetries})...");
                    sleep($waitTime);
                    continue;
                } else {
                    $this->error("Gemini API error response: " . $response->body());
                    return null;
                }
            } catch (\Exception $e) {
                $this->error("Exception occurred during AI analysis: " . $e->getMessage());
                return null;
            }
        }

        return null;
    }

    /**
     * Fetch place details from Google Places API by name
     */
    private function fetchPlaceDetails(string $name): ?array
    {
        $apiKey = env('GOOGLE_PLACES_API_KEY');
        if (empty($apiKey)) {
            $this->error('GOOGLE_PLACES_API_KEY is not set in your .env file.');
            return null;
        }

        try {
            $url = 'https://places.googleapis.com/v1/places:searchText';
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Goog-Api-Key' => $apiKey,
                'X-Goog-FieldMask' => 'places.displayName,places.formattedAddress,places.addressComponents,places.location,places.types,places.nationalPhoneNumber,places.websiteUri,places.regularOpeningHours,places.businessStatus'
            ])->post($url, [
                'textQuery' => $name,
                'languageCode' => 'zh-TW'
            ]);

            if ($response->successful()) {
                $places = $response->json()['places'] ?? [];
                return $places[0] ?? null;
            }
        } catch (\Exception $e) {
            $this->error("Google Places Search failed: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Ingest manual verified venue details into the database
     */
    private function ingestManualVenue(string $name, array $placeDetails): void
    {
        $address = $placeDetails['formattedAddress'] ?? '';
        $venue = FosterVenue::firstOrNew([
            'name' => $placeDetails['displayName']['text'] ?? $name,
            'address' => $address
        ]);

        $components = $placeDetails['addressComponents'] ?? [];
        $city = '';
        $district = '';
        foreach ($components as $c) {
            if (in_array('administrative_area_level_1', $c['types'])) {
                $city = $c['displayName']['text'] ?? '';
            }
            if (in_array('locality', $c['types']) || in_array('sublocality_level_1', $c['types'])) {
                $district = $c['displayName']['text'] ?? '';
            }
        }

        $venue->fill([
            'status' => FosterVenue::STATUS_ACTIVE,
            'is_verified' => true,
            'city' => $city,
            'district' => $district,
            'phone' => $placeDetails['nationalPhoneNumber'] ?? null,
            'latitude' => $placeDetails['location']['latitude'] ?? null,
            'longitude' => $placeDetails['location']['longitude'] ?? null,
            'rating' => $placeDetails['rating'] ?? null,
            'user_rating_count' => $placeDetails['userRatingCount'] ?? null,
            'business_hours' => $placeDetails['regularOpeningHours']['weekdayDescriptions'] ?? null,
            'website_url' => isset($placeDetails['websiteUri']) ? substr($placeDetails['websiteUri'], 0, 255) : null,
            'type' => [FosterVenue::TYPE_RESTAURANT_CAFE], // Defaulting manually added cafes to restaurant tag array
            'business_status' => $placeDetails['businessStatus'] ?? 'OPERATIONAL',
            'services' => ['adoption'],
        ]);

        $venue->save();
        $this->info("Successfully verified and saved {$venue->name} to the database.");
    }
}
