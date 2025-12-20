<?php

namespace App\Console\Commands;

use App\Jobs\CacheTBOHotelInDatabase;
use App\Models\TBOHotel;
use App\Models\Panel\Setting;
use Illuminate\Console\Command;

class CacheTBOHotelInDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cache-t-b-o-hotel-in-database-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache TBO hotel in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $setting = Setting::where('code', 'number_of_days_to_renew_hotel_cache')->first();

        TBOHotel::where('cached_at', '<', now()->subDays(@$setting->value ?: 90))->chunk(50, function ($hotels) {
            CacheTBOHotelInDatabase::dispatch($hotels->pluck('code')->toArray(), app()->getLocale());
        });
    }
}
