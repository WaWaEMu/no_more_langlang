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
        $this->info("🔍 測試 Agent 的眼睛（上網搜尋能力）...");

        // 假設我們要測試找這家名叫 haven hair 的店
        $testQuery = '"haven hair" 中途 送養';

        $this->line("正在向 Google 發送搜尋請求: [ {$testQuery} ]\n");

        $snippets = $this->performSearch($testQuery);

        if (empty($snippets)) {
            $this->error("沒有找到結果，或 API 設定有誤。");
            return;
        }

        $this->info("✅ 成功獲取搜尋摘要！Agent 準備進行思考...\n");
        
        $this->info("🧠 呼叫 Gemini AI 大腦判斷中...");
        $aiResult = $this->analyzeWithAI('haven hair', $snippets);
        
        if ($aiResult) {
            $this->info("\n🎉 AI 判斷完成！以下是回傳的 JSON 結果：\n");
            $this->line(json_encode($aiResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            if ($aiResult['is_foster_venue'] ?? false) {
                $this->info("\n✅ 結論：這是一間真正的中途商家！原因：{$aiResult['reason']}");
            } else {
                $this->warn("\n❌ 結論：這不是中途商家。原因：{$aiResult['reason']}");
            }
        }
    }

    /**
     * Agent 的搜尋工具：透過 Serper API 獲取 Google 搜尋摘要
     */
    private function performSearch(string $query): string
    {
        $apiKey = env('SERPER_API_KEY');
        if (empty($apiKey)) {
            $this->error('請確認 .env 中有設定 SERPER_API_KEY');
            return '';
        }

        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Content-Type' => 'application/json'
            ])->post('https://google.serper.dev/search', [
                        'q' => $query,
                        'gl' => 'tw',        // 定位在台灣
                        'hl' => 'zh-tw',     // 使用繁體中文
                        'num' => 5           // 只抓前 5 筆最相關的，節省 AI 閱讀時間與 Token
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                $snippets = [];

                // 解析 Google 搜尋的「自然搜尋結果 (Organic)」
                if (isset($data['organic'])) {
                    foreach ($data['organic'] as $result) {
                        $title = $result['title'] ?? '';
                        $snippet = $result['snippet'] ?? '';
                        $snippets[] = "標題: {$title}\n摘要: {$snippet}";
                    }
                }

                // 將前 5 筆摘要組合成一段文字回傳給 AI
                return implode("\n---\n", $snippets);
            } else {
                $this->error("Serper API 回應錯誤: " . $response->body());
            }
        } catch (\Exception $e) {
            $this->error("搜尋時發生例外錯誤: " . $e->getMessage());
        }

        return '';
    }
    /**
     * Agent 的思考工具：透過 Gemini API 進行 LLM 邏輯推演
     */
    private function analyzeWithAI(string $name, string $snippets): ?array
    {
        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            $this->error('請確認 .env 中有設定 GEMINI_API_KEY');
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
                    'temperature' => 0.1, // 保持冷靜與客觀，減少幻覺
                    'responseMimeType' => 'application/json' // 強制 JSON 輸出
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // 去除可能出現的 markdown 標籤
                $text = trim(str_replace(['```json', '```'], '', $text));
                
                return json_decode($text, true);
            } else {
                $this->error("Gemini API 回應錯誤: " . $response->body());
            }
        } catch (\Exception $e) {
            $this->error("AI 分析時發生例外錯誤: " . $e->getMessage());
        }

        return null;
    }
}
