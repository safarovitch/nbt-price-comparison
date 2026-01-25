<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExchangeRateController extends Controller
{
    /**
     * Get exchange rates from NBT RSS feed based on current locale
     */
    public function index(Request $request): JsonResponse
    {
        $locale = app()->getLocale();

        // Map locale to RSS feed URL
        // Note: Tajik locale is 'tg' but RSS uses 'tj'
        $rssUrlMap = [
            'en' => 'https://www.nbt.tj/en/kurs/rss.php',
            'ru' => 'https://www.nbt.tj/ru/kurs/rss.php',
            'tg' => 'https://www.nbt.tj/tj/kurs/rss.php',
        ];

        $rssUrl = $rssUrlMap[$locale] ?? $rssUrlMap['en'];
        $cacheKey = "exchange_rates_{$locale}";

        try {
            $rates = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($rssUrl) {
                return $this->fetchAndParseRss($rssUrl);
            });

            return response()->json([
                'success' => true,
                'data' => $rates,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch exchange rates', [
                'locale' => $locale,
                'url' => $rssUrl,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch exchange rates',
                'data' => [],
            ], 500);
        }
    }

    /**
     * Fetch and parse RSS feed
     */
    private function fetchAndParseRss(string $url): array
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'Mozilla/5.0 (compatible; NBT Price Comparison)',
            ],
        ]);

        $xmlContent = @file_get_contents($url, false, $context);

        if ($xmlContent === false) {
            throw new \Exception('Failed to fetch RSS feed');
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlContent);

        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new \Exception('Failed to parse XML: ' . json_encode($errors));
        }

        $rates = [];
        $pubDate = (string) ($xml->channel->pubDate ?? '');
        $description = (string) ($xml->channel->description ?? '');

        foreach ($xml->channel->item as $item) {
            $title = (string) $item->title;

            // Parse title: "NAME: US Dollar | CURRENCY CODE: 840 | UNIT: 1 | RATE: 9.3263"
            if (preg_match('/NAME:\s*(.+?)\s*\|\s*CURRENCY CODE:\s*(\d+)\s*\|\s*UNIT:\s*(\d+)\s*\|\s*RATE:\s*([\d.]+)/', $title, $matches)) {
                $rates[] = [
                    'name' => trim($matches[1]),
                    'code' => (int) $matches[2],
                    'unit' => (int) $matches[3],
                    'rate' => (float) $matches[4],
                ];
            }
        }



        // Sort rates: USD (840), EUR (978), RUB (643/810), CNY (156), KZT (398)
        $priority = [
            840 => 1, // USD
            978 => 2, // EUR
            643 => 3, // RUB
            810 => 3, // RUB (Old)
            156 => 4, // CNY
            398 => 5, // KZT
        ];

        usort($rates, function ($a, $b) use ($priority) {
            $pA = $priority[$a['code']] ?? 999;
            $pB = $priority[$b['code']] ?? 999;

            if ($pA === $pB) {
                return $a['name'] <=> $b['name'];
            }
            return $pA <=> $pB;
        });

        return [
            'pubDate' => $pubDate,
            'description' => $description,
            'rates' => $rates,
        ];
    }
}
