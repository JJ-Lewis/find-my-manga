<?php

namespace App\Services\Retailer\Contracts;

interface RetailerAdapterInterface
{
    /**
     * Search the retailer by the book's isbn number
     * @param string $isbn
     * @return array
     */
    public function searchByIsbn(string $isbn): array;

    public function searchByTitle(string $title): array;

    public function getAvailability(string $skuOrId): array;
}
