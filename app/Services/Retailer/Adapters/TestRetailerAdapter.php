<?php

namespace App\Services\Retailer\Adapters;

use App\Services\Retailer\Contracts\RetailerAdapterInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestRetailerAdapter implements RetailerAdapterInterface
{
    protected string $base;
    protected string $apiKey;

    public function __construct()
    {
        $cfg = config('retailers.test');
        $this->base = rtrim($cfg['base_url'], '/');
        $this->apiKey = $cfg['api_key'];
    }

    protected function request(string $path, array $params = []): ?array
    {
        $res = Http::withHeaders([
            'Accept'    => 'application/json',
            'X-Api-Key' => $this->apiKey,
        ])->get("{$this->base}{$path}", $params);

        if ($res->failed()) {
            Log::warning('Test API failed', [
                'status' => $res->status(),
                'path'   => $path,
                'body'   => $res->body(),
            ]);
            return null;
        }

        return $res->json();
    }

    public function searchByIsbn(string $isbn): array
    {
        $json = $this->request('/v1/search', ['isbn' => $isbn]);
        if (!$json || empty($json['items'])) {
            return [];
        }

        return array_map(fn ($item) => [
            'sku'         => $item['sku'] ?? null,
            'url'         => $item['url'] ?? null,
            'price_cents' => isset($item['price']) ? (int) round($item['price'] * 100) : null,
            'currency'    => $item['currency'] ?? 'USD',
            'in_stock'    => ($item['availability'] ?? '') === 'in_stock',
            'stock_qty'   => $item['qty'] ?? null,
            'raw'         => $item,
        ], $json['items']);
    }

    public function searchByTitle(string $title): array
    {
        $json = $this->request('/v1/search', ['q' => $title]);

        if (!$json || empty($json['items'])) {
            return [];
        }

        return array_map(fn ($item) => [
            'sku'         => $item['sku'] ?? null,
            'url'         => $item['url'] ?? null,
            'price_cents' => isset($item['price']) ? (int) round($item['price'] * 100) : null,
            'currency'    => $item['currency'] ?? 'USD',
            'in_stock'    => ($item['availability'] ?? '') === 'in_stock',
            'stock_qty'   => $item['qty'] ?? null,
            'raw'         => $item,
        ], $json['items']);
    }

    public function getAvailability(string $skuOrId): array
    {
        $json = $this->request("/v1/items/{$skuOrId}");

        if (!$json) {
            return [];
        }

        return [[
            'sku'         => $json['sku'] ?? $skuOrId,
            'url'         => $json['url'] ?? null,
            'price_cents' => isset($json['price']) ? (int) round($json['price'] * 100) : null,
            'currency'    => $json['currency'] ?? 'USD',
            'in_stock'    => ($json['availability'] ?? '') === 'in_stock',
            'stock_qty'   => $json['qty'] ?? null,
            'raw'         => $json,
        ]];
    }
}
