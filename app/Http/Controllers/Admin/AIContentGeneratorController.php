<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AIContentGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AIContentGeneratorController extends Controller
{
    protected $aiService;

    public function __construct(AIContentGeneratorService $aiService)
    {
        $this->aiService = $aiService;
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->user_type !== 'admin' && !auth()->user()->hasRole('admin')) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    /**
     * Generate course overview
     */
    public function generateOverview(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'course_title' => 'required|string|max:255',
                'course_description' => 'nullable|string|max:1000',
                'target_audience' => 'nullable|string|max:500',
            ]);

            $overview = $this->aiService->generateOverview(
                $validated['course_title'],
                $validated['course_description'] ?? '',
                $validated['target_audience'] ?? ''
            );

            return response()->json([
                'success' => true,
                'overview' => $overview,
            ]);
        } catch (\Exception $e) {
            \Log::error('Overview generation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate overview: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate course outline
     */
    public function generateOutline(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'course_title' => 'required|string|max:255',
                'course_description' => 'nullable|string|max:1000',
                'target_audience' => 'nullable|string|max:500',
                'number_of_modules' => 'nullable|integer|min:3|max:15',
            ]);

            $outline = $this->aiService->generateOutline(
                $validated['course_title'],
                $validated['course_description'] ?? '',
                $validated['number_of_modules'] ?? 5
            );

            return response()->json([
                'success' => true,
                'outline' => $outline,
            ]);
        } catch (\Exception $e) {
            \Log::error('Outline generation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate outline: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate full course content (overview + outline)
     */
    public function generateContent(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'course_title' => 'required|string|max:255',
                'course_description' => 'nullable|string|max:1000',
                'target_audience' => 'nullable|string|max:500',
                'number_of_modules' => 'nullable|integer|min:3|max:15',
            ]);

            $overview = $this->aiService->generateOverview(
                $validated['course_title'],
                $validated['course_description'] ?? '',
                $validated['target_audience'] ?? ''
            );

            $outline = $this->aiService->generateOutline(
                $validated['course_title'],
                $validated['course_description'] ?? '',
                $validated['number_of_modules'] ?? 5
            );

            return response()->json([
                'success' => true,
                'overview' => $overview,
                'outline' => $outline,
            ]);
        } catch (\Exception $e) {
            \Log::error('Content generation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate content: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available LLM providers
     */
    public function getProviders(): JsonResponse
    {
        $currentProvider = env('LLM_PROVIDER', 'openai');
        
        $providers = [
            ['id' => 'openai', 'name' => 'OpenAI (GPT)', 'configured' => !empty(env('LLM_KEY_OPENAI'))],
            ['id' => 'anthropic', 'name' => 'Anthropic (Claude)', 'configured' => !empty(env('LLM_KEY_ANTHROPIC'))],
            ['id' => 'cohere', 'name' => 'Cohere', 'configured' => !empty(env('LLM_KEY_COHERE'))],
            ['id' => 'huggingface', 'name' => 'Hugging Face', 'configured' => !empty(env('LLM_KEY_HUGGINGFACE'))],
            ['id' => 'gemini', 'name' => 'Google Gemini', 'configured' => !empty(env('LLM_KEY_GEMINI'))],
            ['id' => 'openrouter', 'name' => 'OpenRouter', 'configured' => !empty(env('LLM_KEY_OPENROUTER'))],
            ['id' => 'grok', 'name' => 'Grok (xAI)', 'configured' => !empty(env('LLM_KEY_GROK'))],
        ];

        return response()->json([
            'success' => true,
            'current_provider' => $currentProvider,
            'providers' => $providers,
        ]);
    }
}
