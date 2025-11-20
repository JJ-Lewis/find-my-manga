<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Retailer;

class RetailerSeeder extends Seeder
{
    public function run(): void
    {
        Retailer::firstOrCreate(
            ['name' => 'BooksNow'],
            [
                'type'   => 'api',
                'base_url' => config('retailers.booksnow.base_url'),
                'config'=> [
                    'adapter_key' => 'booksnow',
                ],
            ]
        );

        // Example scrape-based retailer
        Retailer::firstOrCreate(
            ['name' => 'OldBookStore'],
            [
                'type' => 'scrape',
                'base_url' => 'https://oldbookstore.test',
                'config' => [
                    'start_urls' => [
                        'https://oldbookstore.test/books',
                    ],
                    'selectors' => [
                        'product_link' => '.book-card a',
                        'title'        => 'h1.book-title',
                        'price'        => '.book-price',
                        'availability' => '.stock-status',
                    ],
                    'currency' => 'USD',
                ],
            ]
        );
    }
}
