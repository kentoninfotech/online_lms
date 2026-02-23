<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class AIContentGeneratorService
{
    protected $provider;
    protected $apiKey;
    protected $model;
    protected $maxTokens;

    public function __construct()
    {
        $this->provider = config('app.llm_provider', 'openai');
        $this->apiKey = $this->getApiKey();
        $this->model = config('app.llm_model_default', 'gpt-3.5-turbo');
        $this->maxTokens = config('app.llm_max_tokens', 2000);
    }

    /**
     * Generate course content (overview + outline)
     * 
     * @param string $courseTitle
     * @param string $courseDescription
     * @param string $targetAudience
     * @return array with 'overview' and 'outline' keys
     */
    public function generateCourseContent(string $courseTitle, string $courseDescription = '', string $targetAudience = ''): array
    {
        if (!$this->apiKey) {
            throw new Exception("No LLM API key configured for {$this->provider}");
        }

        $prompt = $this->buildCoursePrompt($courseTitle, $courseDescription, $targetAudience);

        try {
            $response = $this->callLLM($prompt);
            return $this->parseCourseResponse($response);
        } catch (Exception $e) {
            \Log::error('AI Content Generation Error', [
                'provider' => $this->provider,
                'error' => $e->getMessage(),
                'course' => $courseTitle,
            ]);
            throw $e;
        }
    }

    /**
     * Generate course overview (5 paragraphs)
     * 
     * @param string $courseTitle
     * @param string $courseDescription
     * @return string HTML formatted overview
     */
    public function generateOverview(string $courseTitle, string $courseDescription = ''): string
    {
        $prompt = "Create a comprehensive 5-paragraph course overview for '{$courseTitle}'.";
        
        if (!empty($courseDescription)) {
            $prompt .= " Based on: {$courseDescription}";
        }
        
        $prompt .= "\n\nFormat the output as 5 distinct paragraphs. Each paragraph should be 3-4 sentences. Make it professional and engaging.";
        
        try {
            $response = $this->callLLM($prompt);
            return $this->formatOverviewAsHTML($response);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Generate course outline (structured chapters/modules)
     * 
     * @param string $courseTitle
     * @param string $courseDescription
     * @param int $numberOfModules
     * @return string HTML formatted outline
     */
    public function generateOutline(string $courseTitle, string $courseDescription = '', int $numberOfModules = 5): string
    {
        $prompt = "Create a detailed course outline/curriculum for '{$courseTitle}' with {$numberOfModules} main modules.";
        
        if (!empty($courseDescription)) {
            $prompt .= " Based on: {$courseDescription}";
        }
        
        $prompt .= "\n\nFor each module, provide:
- Module Title
- Duration estimate
- Key topics (3-5 bullet points)
- Learning objectives (2-3 points)

Format this as a structured outline that can be easily converted to HTML.";
        
        try {
            $response = $this->callLLM($prompt);
            return $this->formatOutlineAsHTML($response);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get API key for current provider
     */
    private function getApiKey(): ?string
    {
        return match ($this->provider) {
            'openai' => env('LLM_KEY_OPENAI'),
            'anthropic' => env('LLM_KEY_ANTHROPIC'),
            'cohere' => env('LLM_KEY_COHERE'),
            'huggingface' => env('LLM_KEY_HUGGINGFACE'),
            'gemini' => env('LLM_KEY_GEMINI'),
            'openrouter' => env('LLM_KEY_OPENROUTER'),
            'grok' => env('LLM_KEY_GROK'),
            default => null,
        };
    }

    /**
     * Call LLM API based on provider
     */
    private function callLLM(string $prompt): string
    {
        return match ($this->provider) {
            'openai' => $this->callOpenAI($prompt),
            'anthropic' => $this->callAnthropic($prompt),
            'cohere' => $this->callCohere($prompt),
            'huggingface' => $this->callHuggingFace($prompt),
            'gemini' => $this->callGemini($prompt),
            'openrouter' => $this->callOpenRouter($prompt),
            'grok' => $this->callGrok($prompt),
            default => throw new Exception("Unsupported LLM provider: {$this->provider}"),
        };
    }

    /**
     * Call OpenAI API
     */
    private function callOpenAI(string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an expert course designer and content writer. Create educational content that is clear, engaging, and professional.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => $this->maxTokens,
            'temperature' => 0.7,
        ]);

        if ($response->failed()) {
            throw new Exception("OpenAI API error: " . $response->body());
        }

        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? '';
    }

    /**
     * Call Anthropic (Claude) API
     */
    private function callAnthropic(string $prompt): string
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'system' => 'You are an expert course designer and content writer. Create educational content that is clear, engaging, and professional.'
        ]);

        if ($response->failed()) {
            throw new Exception("Anthropic API error: " . $response->body());
        }

        $data = $response->json();
        return $data['content'][0]['text'] ?? '';
    }

    /**
     * Call Cohere API
     */
    private function callCohere(string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.cohere.ai/v1/generate', [
            'prompt' => $prompt,
            'max_tokens' => $this->maxTokens,
            'temperature' => 0.8,
            'k' => 0,
            'p' => 0.75,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
            'return_likelihoods' => 'NONE',
        ]);

        if ($response->failed()) {
            throw new Exception("Cohere API error: " . $response->body());
        }

        $data = $response->json();
        return $data['generations'][0]['text'] ?? '';
    }

    /**
     * Call Hugging Face API
     */
    private function callHuggingFace(string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api-inference.huggingface.co/models/gpt2', [
            'inputs' => $prompt,
            'options' => [
                'wait_for_model' => true
            ]
        ]);

        if ($response->failed()) {
            throw new Exception("Hugging Face API error: " . $response->body());
        }

        $data = $response->json();
        return $data[0]['generated_text'] ?? '';
    }

    /**
     * Call Google Gemini API
     */
    private function callGemini(string $prompt): string
    {
        $response = Http::post('https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent', [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ], [
            'key' => $this->apiKey
        ]);

        if ($response->failed()) {
            throw new Exception("Gemini API error: " . $response->body());
        }

        $data = $response->json();
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }

    /**
     * Call OpenRouter API
     */
    private function callOpenRouter(string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'HTTP-Referer' => env('APP_URL', 'http://localhost'),
            'X-Title' => 'Online LMS',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'openai/gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => $this->maxTokens
        ]);

        if ($response->failed()) {
            throw new Exception("OpenRouter API error: " . $response->body());
        }

        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? '';
    }

    /**
     * Call Grok API (via OpenRouter)
     */
    private function callGrok(string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'HTTP-Referer' => env('APP_URL', 'http://localhost'),
            'X-Title' => 'Online LMS',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'x-ai/grok-2',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => $this->maxTokens
        ]);

        if ($response->failed()) {
            throw new Exception("Grok API error: " . $response->body());
        }

        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? '';
    }

    /**
     * Build course content generation prompt
     */
    private function buildCoursePrompt(string $courseTitle, string $courseDescription = '', string $targetAudience = ''): string
    {
        $prompt = "Create comprehensive course content for: '{$courseTitle}'\n\n";
        
        if (!empty($courseDescription)) {
            $prompt .= "Course Description: {$courseDescription}\n";
        }
        
        if (!empty($targetAudience)) {
            $prompt .= "Target Audience: {$targetAudience}\n";
        }
        
        $prompt .= "\n=== OVERVIEW ===\nProvide a 5-paragraph professional course overview. Each paragraph should be 3-4 sentences.\n";
        $prompt .= "\n=== COURSE OUTLINE ===\nProvide a detailed 5-module course outline with:\n";
        $prompt .= "- Module Title\n";
        $prompt .= "- Duration estimate\n";
        $prompt .= "- Key topics (3-5 bullet points)\n";
        $prompt .= "- Learning objectives (2-3 points)\n";
        
        return $prompt;
    }

    /**
     * Parse LLM response into overview and outline
     */
    private function parseCourseResponse(string $response): array
    {
        // Split by "=== COURSE OUTLINE ===" or similar markers
        $parts = preg_split('/={3,}\s*(COURSE\s+)?OUTLINE\s*={3,}/i', $response);
        
        $overview = trim($parts[0] ?? '');
        $outline = trim($parts[1] ?? '');
        
        // Remove extra markers if present
        $overview = preg_replace('/^={3,}[\s\w]+={3,}/i', '', $overview);
        
        return [
            'overview' => $this->formatOverviewAsHTML($overview),
            'outline' => $this->formatOutlineAsHTML($outline),
        ];
    }

    /**
     * Format overview text as HTML
     */
    private function formatOverviewAsHTML(string $overview): string
    {
        // Split into paragraphs
        $paragraphs = array_filter(
            array_map('trim', explode("\n\n", $overview)),
            fn($p) => !empty($p)
        );
        
        $html = '<div class="course-overview">';
        
        foreach ($paragraphs as $para) {
            // Clean up the paragraph
            $para = trim(preg_replace('/^[\d\-\*•]+\s*/', '', $para));
            
            if (!empty($para)) {
                $html .= '<p>' . htmlspecialchars($para) . '</p>';
            }
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Format outline text as HTML
     */
    private function formatOutlineAsHTML(string $outline): string
    {
        $html = '<div class="course-outline"><ol class="modules-list">';
        
        // Split by module markers (Module, Chapter, Unit, etc.)
        $modules = preg_split('/(?:^|\n)(?:MODULE|CHAPTER|UNIT|SECTION)\s+[\d\-]*\s*[:.]?\s*/i', $outline, -1, PREG_SPLIT_NO_EMPTY);
        
        foreach ($modules as $index => $moduleContent) {
            if (empty(trim($moduleContent))) {
                continue;
            }
            
            $moduleContent = trim($moduleContent);
            
            // Extract module title (usually first line)
            $lines = explode("\n", $moduleContent);
            $title = array_shift($lines);
            $title = trim(preg_replace('/^[\d\-\*\.\)]+\s*/', '', $title));
            
            $html .= '<li class="module-item">';
            $html .= '<h4 class="module-title">' . htmlspecialchars($title) . '</h4>';
            
            // Process remaining content
            $remainingContent = trim(implode("\n", $lines));
            
            // Look for duration
            if (preg_match('/(?:Duration|Hours?)[\s:]*([^\n]*)/i', $remainingContent, $matches)) {
                $html .= '<p class="module-duration"><strong>Duration:</strong> ' . htmlspecialchars(trim($matches[1])) . '</p>';
            }
            
            // Extract topics (bullet points or numbered lists)
            if (preg_match('/(?:Topics|Key\s+Topics)[\s:]*\n?((?:[•\-\*\d\.]\s*[^\n]+\n?)+)/i', $remainingContent, $matches)) {
                $topics = array_filter(array_map('trim', preg_split('/[•\-\*\d\.]\s*/', trim($matches[1]))));
                if (!empty($topics)) {
                    $html .= '<div class="module-topics"><strong>Topics:</strong><ul>';
                    foreach ($topics as $topic) {
                        if (!empty($topic)) {
                            $html .= '<li>' . htmlspecialchars($topic) . '</li>';
                        }
                    }
                    $html .= '</ul></div>';
                }
            }
            
            // Extract learning objectives
            if (preg_match('/(?:Objectives|Learning\s+Objectives)[\s:]*\n?((?:[•\-\*\d\.]\s*[^\n]+\n?)+)/i', $remainingContent, $matches)) {
                $objectives = array_filter(array_map('trim', preg_split('/[•\-\*\d\.]\s*/', trim($matches[1]))));
                if (!empty($objectives)) {
                    $html .= '<div class="module-objectives"><strong>Learning Objectives:</strong><ul>';
                    foreach ($objectives as $obj) {
                        if (!empty($obj)) {
                            $html .= '<li>' .htmlspecialchars($obj) . '</li>';
                        }
                    }
                    $html .= '</ul></div>';
                }
            }
            
            $html .= '</li>';
        }
        
        $html .= '</ol></div>';
        
        return $html;
    }
}
