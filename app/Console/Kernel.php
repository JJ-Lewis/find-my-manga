<?php


namespace App\Console;

use App\Jobs\Imports\ScrapeRetailerJob;
use App\Models\Retailer;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            Retailer::where('type', 'scrape')->get()->each(function (Retailer $retailer) {
                ScrapeRetailerJob::dispatch($retailer);
            });
        })->hourly();
    }
}
