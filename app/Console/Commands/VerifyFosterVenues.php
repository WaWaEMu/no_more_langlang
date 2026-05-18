<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class VerifyFosterVenues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:verify-venues';

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
        $this->info("🔍 Testing Agent's web search capability...");

        // Suppose we are testing the search for this store named 'haven hair'
        $testQuery = '"haven hair" 中途 送養';

        $this->line("Sending search request to Google: [ {$testQuery} ]\n");

        $snippets = $this->performSearch($testQuery);

        if (empty($snippets)) {
            $this->error("No results found, or API configuration is incorrect.");
            return;
        }

        $this->info("✅ Successfully retrieved search snippets! Agent is preparing to analyze...\n");
        
        $this->info("🧠 Calling Gemini AI brain for judgment...");
        $aiResult = $this->analyzeWithAI('haven hair', $snippets);
        
        if ($aiResult) {
            $this->info("\n🎉 AI analysis complete! Here is the returned JSON result:\n");
            $this->line(json_encode($aiResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            if ($aiResult['is_foster_venue'] ?? false) {
                $this->info("\n✅ Conclusion: This is a genuine foster venue! Reason: {$aiResult['reason']}");
            } else {
                $this->warn("\n❌ Conclusion: This is not a foster venue. Reason: {$aiResult['reason']}");
            }
        }
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
    private function analyzeWithAI(string $name, string $snippets): ?array
    {
        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            $this->error('Please ensure GEMINI_API_KEY is set in your .env file.');
            return null;
        }

        $prompt = "你是一位台灣流浪動物中途商家的嚴格審核專家。\n"
            . "請根據以下 Google 搜尋摘要，判斷這家店「{$name}」是否有提供流浪貓狗的認養、中途或送養服務。\n\n"
            . "搜尋摘要：\n{$snippets}\n\n"
            . "請務必只回傳純 JSON 格式，不要包含 Markdown 語法（例如 ```json），格式如下：\n"
            . "{\n"
            . '  "is_foster_venue": true 或 false,' . "\n"
            . '  "confidence_score": 0到100的整數,' . "\n"
            . '  "reason": "你的判斷理由（繁體中文，限 50 字以內）"' . "\n"
            . "}";

        try {
            // Gemini API endpoint (Using the latest Flash model)
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}";
            
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
            } else {
                $this->error("Gemini API error response: " . $response->body());
            }
        } catch (\Exception $e) {
            $this->error("Exception occurred during AI analysis: " . $e->getMessage());
        }

        return null;
    }
}
