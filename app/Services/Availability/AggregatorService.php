<?php

namespace App\Services\Availability;

use App\Models\Retailer;
use App\Models\RetailerProduct;
use App\Services\Retailer\Contracts\RetailerAdapterInterface;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class AggregatorService
{
    protected int $cacheTtl = 120;

    public function aggregateByIsbn(string $isbn): array
    {
        $key = "availability:isbn:{$isbn}";

        return Cache::remember($key, $this->cacheTtl, function () use ($isbn) {
            $results = [];

            $retailers = Retailer::all();

            foreach ($retailers as $retailer) {
                if ($retailer->type === 'api') {
                    $adapter = $this->resolveAdapter($retailer);
                    if (!$adapter) {
                        continue;
                    }

                    $items = $adapter->searchByIsbn($isbn);
                    foreach ($items as $item) {
                        $results[] = $this->normalizeResult($retailer->name, 'api', $item);
                    }
                } else {
                    // scraper-backed
                    $products = RetailerProduct::where('retailer_id', $retailer->id)
                        ->where(function ($q) use ($isbn) {
                            $q->whereJsonContains('raw_response->isbns', $isbn)
                                ->orWhere('sku', $isbn)
                                ->orWhere('url', 'like', "%{$isbn}%");
                        })->get();

                    foreach ($products as $p) {
                        $results[] = [
                            'retailer'        => $retailer->name,
                            'sku'             => $p->sku,
                            'url'             => $p->url,
                            'price_cents'     => $p->price_cents,
                            'currency'        => $p->currency,
                            'in_stock'        => (bool) $p->in_stock,
                            'stock_qty'       => $p->stock_qty,
                            'last_checked_at' => optional($p->last_checked_at)->toDateTimeString(),
                            'source'          => 'scraped_db',
                            'raw'             => $p->raw_response,
                        ];
                    }
                }
            }

            return $results;
        });
    }

    protected function resolveAdapter(Retailer $retailer): ?RetailerAdapterInterface
    {
        $adapterKey = data_get($retailer->config, 'adapter_key');
        if (!$adapterKey) {
            return null;
        }

        $map = config('retailers.adapters');
        $class = $map[$adapterKey] ?? null;
        if (!$class) {
            return null;
        }

        /** @var RetailerAdapterInterface $adapter */
        $adapter = App::make($class);

        return $adapter;
    }

    protected function normalizeResult(string $retailerName, string $source, array $item): array
    {
        return [
            'retailer'        => $retailerName,
            'sku'             => $item['sku'] ?? null,
            'url'             => $item['url'] ?? null,
            'price_cents'     => $item['price_cents'] ?? null,
            'currency'        => $item['currency'] ?? null,
            'in_stock'        => (bool) ($item['in_stock'] ?? false),
            'stock_qty'       => $item['stock_qty'] ?? null,
            'last_checked_at' => now()->toDateTimeString(),
            'source'          => $source,
            'raw'             => $item['raw'] ?? null,
        ];
    }
}
