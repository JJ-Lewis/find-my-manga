<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RetailerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // nothing huge yet; config-based resolution is in AggregatorService
    }

    public function boot(): void
    {
        //
    }
}
