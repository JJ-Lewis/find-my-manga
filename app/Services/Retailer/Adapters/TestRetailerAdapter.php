<?php

namespace App\Services\Retailer\Adapters;

use App\Services\Retailer\Contracts\RetailerAdapterInterface;

class TestRetailerAdapter implements RetailerAdapterInterface
{
    /**
     * Search the retailer by the book's isbn
     * @param string $isbn
     * @return array
     */
    public function searchByIsbn(string $isbn): array
    {
        return [];
    }

    public function searchByTitle(string $title): array
    {
        return [];
    }

    public function getAvailability(string $skuOrId): array
    {
        return [];
    }
}
