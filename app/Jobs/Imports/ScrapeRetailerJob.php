<?php

namespace App\Jobs\Imports;

use App\Models\Retailer;
use App\Models\RetailerProduct;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ScrapeRetailerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected Retailer $retailer
    ) {}

    public function handle(): void
    {
        $config = $this->retailer->config ?? [];

        if (!isset($config['start_urls'], $config['selectors'])) {
            return;
        }

        $headers = [
            'User-Agent' => config('scraping.user_agent'),
        ];

        foreach ($config['start_urls'] as $url) {
            $res = Http::withHeaders($headers)
                ->timeout(config('scraping.timeout', 10))
                ->get($url);

            if ($res->failed()) {
                continue;
            }

            $crawler = new Crawler($res->body());
            $linkSelector = $config['selectors']['product_link'] ?? 'a.product';

            $crawler->filter($linkSelector)->each(function (Crawler $node) use ($config) {
                $href = $node->attr('href');
                if (!$href) {
                    return;
                }

                $fullUrl = $this->normalizeUrl($href, $this->retailer->base_url);
                $this->scrapeProductPage($fullUrl, $config);
            });
        }
    }

    protected function scrapeProductPage(string $url, array $config): void
    {
        $res = Http::withHeaders([
            'User-Agent' => config('scraping.user_agent'),
        ])
            ->timeout(config('scraping.timeout', 10))
            ->get($url);

        if ($res->failed()) {
            return;
        }

        $html = $res->body();
        $crawler = new Crawler($html);

        $titleSelector = $config['selectors']['title'] ?? 'h1';
        $priceSelector = $config['selectors']['price'] ?? '.price';
        $availabilitySelector = $config['selectors']['availability'] ?? '.availability';

        $title = $crawler->filter($titleSelector)->count()
            ? trim($crawler->filter($titleSelector)->text())
            : null;

        $price = $crawler->filter($priceSelector)->count()
            ? trim($crawler->filter($priceSelector)->text())
            : null;

        $availText = $crawler->filter($availabilitySelector)->count()
            ? trim($crawler->filter($availabilitySelector)->text())
            : null;

        preg_match('/(97(8|9))?\d{9}(\d|X)/', $html, $matches);
        $isbn = $matches[0] ?? null;

        RetailerProduct::updateOrCreate(
            [
                'retailer_id' => $this->retailer->id,
                'url'         => $url,
            ],
            [
                'sku'             => null,
                'book_id'         => null,
                'price_cents'     => $this->parsePriceToCents($price),
                'currency'        => $config['currency'] ?? config('retailers.defaults.currency', 'USD'),
                'in_stock'        => $this->parseAvailability($availText),
                'stock_qty'       => null,
                'raw_response'    => [
                    'page_title' => $title,
                    'isbn'       => $isbn,
                ],
                'last_checked_at' => now(),
            ]
        );
    }

    protected function parsePriceToCents(?string $priceString): ?int
    {
        if (!$priceString) {
            return null;
        }

        $num = preg_replace('/[^\d\.]/', '', $priceString);
        return (int) round((float) $num * 100);
    }

    protected function parseAvailability(?string $text): bool
    {
        if (!$text) {
            return false;
        }

        $text = strtolower($text);

        return str_contains($text, 'in stock') ||
            str_contains($text, 'available') ||
            str_contains($text, 'ships');
    }

    protected function normalizeUrl(string $href, ?string $baseUrl): string
    {
        if (str_starts_with($href, 'http')) {
            return $href;
        }

        return rtrim($baseUrl ?: '', '/') . '/' . ltrim($href, '/');
    }
}
