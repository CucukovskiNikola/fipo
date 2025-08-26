<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class TranslationController extends Controller
{
    /**
     * Translate text from German to English using external APIs
     */
    public function translate(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|max:5000',
            'from' => 'string|max:5',
            'to' => 'string|max:5'
        ]);

        $text = $request->input('text');
        $from = $request->input('from', 'de');
        $to = $request->input('to', 'en');

        // Return original text if empty
        if (empty(trim($text))) {
            return response()->json(['translatedText' => $text]);
        }

        // Create cache key
        $cacheKey = "translation:{$from}:{$to}:" . md5($text);

        // Check cache first (cache for 24 hours)
        $cachedTranslation = Cache::get($cacheKey);
        if ($cachedTranslation) {
            return response()->json([
                'translatedText' => $cachedTranslation,
                'service' => 'cache',
                'cached' => true
            ]);
        }

        try {
            // Try Libre Translate first if configured
            $translatedText = $this->translateWithLibreTranslate($text, $from, $to);
            
            if ($translatedText && $translatedText !== $text) {
                // Cache the successful translation
                Cache::put($cacheKey, $translatedText, now()->addHours(24));
                
                return response()->json([
                    'translatedText' => $translatedText,
                    'service' => 'LibreTranslate'
                ]);
            }

            // Fallback to MyMemory API
            $translatedText = $this->translateWithMyMemory($text, $from, $to);
            
            if ($translatedText && $translatedText !== $text) {
                // Cache the successful translation
                Cache::put($cacheKey, $translatedText, now()->addHours(24));
                
                return response()->json([
                    'translatedText' => $translatedText,
                    'service' => 'MyMemory'
                ]);
            }

            // If all services fail, return original text
            return response()->json([
                'translatedText' => $text,
                'service' => 'fallback'
            ]);

        } catch (\Exception $e) {
            Log::error('Translation failed', [
                'text' => $text,
                'from' => $from,
                'to' => $to,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'translatedText' => $text,
                'service' => 'error'
            ]);
        }
    }

    /**
     * Translate using Libre Translate API
     */
    private function translateWithLibreTranslate(string $text, string $from, string $to): ?string
    {
        $libreTranslateUrl = Config::get('services.libretranslate.url');
        $apiKey = Config::get('services.libretranslate.api_key');
        
        if (!$libreTranslateUrl) {
            return null;
        }

        try {
            $payload = [
                'q' => $text,
                'source' => $from,
                'target' => $to,
                'format' => 'text'
            ];

            // Add API key if available
            if ($apiKey) {
                $payload['api_key'] = $apiKey;
            }

            $response = Http::timeout(15)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'Partner Management App'
                ])
                ->post($libreTranslateUrl . '/translate', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['translatedText'])) {
                    $translatedText = $data['translatedText'];
                    
                    // Basic quality check
                    if (!empty($translatedText) && $translatedText !== $text) {
                        Log::info('Libre Translate successful', [
                            'text' => $text,
                            'translated' => $translatedText,
                            'from' => $from,
                            'to' => $to
                        ]);
                        return $translatedText;
                    }
                }
            }

            Log::warning('Libre Translate failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

        } catch (\Exception $e) {
            Log::warning('Libre Translate error', [
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Translate using MyMemory API
     */
    private function translateWithMyMemory(string $text, string $from, string $to): ?string
    {
        try {
            $response = Http::timeout(10)->get('https://api.mymemory.translated.net/get', [
                'q' => $text,
                'langpair' => "{$from}|{$to}"
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['responseData']['translatedText'])) {
                    $translatedText = $data['responseData']['translatedText'];
                    
                    // Basic quality check
                    if (!empty($translatedText) && $translatedText !== $text) {
                        Log::info('MyMemory translation successful', [
                            'text' => $text,
                            'translated' => $translatedText
                        ]);
                        return $translatedText;
                    }
                }
            }

        } catch (\Exception $e) {
            Log::warning('MyMemory translation error', [
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Get translation statistics
     */
    public function stats(): JsonResponse
    {
        $cachePrefix = 'translation:';
        $cacheKeys = Cache::get('translation_keys', []);
        
        $stats = [
            'total_cached' => count($cacheKeys),
            'cache_size_mb' => 0,
            'languages' => [],
            'services' => []
        ];

        // Calculate cache size and language distribution
        foreach ($cacheKeys as $key) {
            $translation = Cache::get($key);
            if ($translation) {
                $stats['cache_size_mb'] += strlen($translation);
                
                // Parse language info from key
                if (preg_match('/translation:([a-z]{2}):([a-z]{2}):/', $key, $matches)) {
                    $from = $matches[1];
                    $to = $matches[2];
                    $langPair = "{$from}-{$to}";
                    
                    if (!isset($stats['languages'][$langPair])) {
                        $stats['languages'][$langPair] = 0;
                    }
                    $stats['languages'][$langPair]++;
                }
            }
        }

        $stats['cache_size_mb'] = round($stats['cache_size_mb'] / 1024 / 1024, 2);

        return response()->json($stats);
    }

    /**
     * Clear translation cache
     */
    public function clearCache(): JsonResponse
    {
        $cacheKeys = Cache::get('translation_keys', []);
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        
        Cache::forget('translation_keys');
        
        Log::info('Translation cache cleared');
        
        return response()->json(['message' => 'Translation cache cleared successfully']);
    }

    /**
     * Check if text appears to be German
     */
    public function isGerman(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|max:1000'
        ]);

        $text = $request->input('text');
        $isGerman = $this->detectGerman($text);

        return response()->json(['isGerman' => $isGerman]);
    }

    /**
     * Detect if text is likely German
     */
    private function detectGerman(string $text): bool
    {
        $germanWords = ['der', 'die', 'das', 'und', 'ist', 'mit', 'für', 'auf', 'zu', 'von', 'bei', 'auch', 'eine', 'einen', 'einer'];
        $words = preg_split('/\s+/', strtolower($text));
        $germanWordCount = count(array_intersect($words, $germanWords));
        
        return $germanWordCount > 0 || preg_match('/[äöüß]/', $text);
    }
}
