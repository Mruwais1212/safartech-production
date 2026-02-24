<?php

namespace App\Providers;

use App\Notifications\Channel\BalanceChannel;
use App\Notifications\Channel\CustomDatabaseChannel;
use App\Notifications\Channel\FcmChannel;
use App\Notifications\Channel\SmsChannel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Notification::extend('fcm', function ($app) {
            return new FcmChannel;
        });

        Notification::extend('custom_database', function ($app) {
            return new CustomDatabaseChannel;
        });

        Notification::extend('sms', function ($app) {
            return new SmsChannel;
        });

        Notification::extend('balance', function ($app) {
            return new BalanceChannel;
        });

        RateLimiter::for('search', function ($request) {
            return $request->user('web')
                ? Limit::perMinute(30)->by('user:' . $request->user('web')->id)
                : Limit::perMinute(10)->by('ip:' . $request->ip());
        });

        RateLimiter::for('ai-generation', function ($request) {
            return $request->user('web')
                ? Limit::perMinute(30)->by('user:' . $request->user('web')->id)
                : Limit::perMinute(10)->by('ip:' . $request->ip());
        });

        RateLimiter::for('payment-init', function ($request) {
            return $request->user('web')
                ? Limit::perMinute(5)->by('user:' . $request->user('web')->id)
                : Limit::perMinute(5)->by('ip:' . $request->ip());
        });

        RateLimiter::for('cancel', function ($request) {
            return $request->user('web')
                ? Limit::perMinute(5)->by('user:' . $request->user('web')->id)
                : Limit::perMinute(5)->by('ip:' . $request->ip());
        });

        RateLimiter::for('admin-ops', function ($request) {
            $admin = $request->user('admin');

            return Limit::perMinute(10)->by($admin ? 'admin:' . $admin->id : 'ip:' . $request->ip());
        });

        if (! Collection::hasMacro('paginate')) {
            Collection::macro(
                'paginate',
                function ($perPage = 15, $page = null, $options = []) {
                    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

                    return (new LengthAwarePaginator(
                        items: $this->forPage(page: $page, perPage: $perPage),
                        total: $this->count(),
                        perPage: $perPage,
                        currentPage: $page,
                        options: $options
                    ))
                        ->withPath('');
                }
            );
        }
    }
}
