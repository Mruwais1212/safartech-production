<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'])
                ->prefix(LaravelLocalization::setLocale().'/admin-panel')
                ->name('panel.')
                ->namespace('App\Http\Controllers\Panel')
                ->group(base_path('routes/panel.php'));
            Route::middleware(['web', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'])
                ->prefix(LaravelLocalization::setLocale().'/')
                ->namespace('App\Http\Controllers\Website')
                ->group(base_path('routes/web.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'webhooks/moyasar',
        ]);

        $middleware->alias([
            'localize' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
            'permission' => \App\Http\Middleware\Permission::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'check-guard' => \App\Http\Middleware\CheckGuard::class,
            'correlation-id' => \App\Http\Middleware\CorrelationIdMiddleware::class,
            'csp-report-only' => \App\Http\Middleware\ContentSecurityPolicyReportOnly::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\CorrelationIdMiddleware::class,
            \App\Http\Middleware\ContentSecurityPolicyReportOnly::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $exception, Request $request) {
            if ($request->expectsJson()) {
                if ($exception instanceof ValidationException) {
                    $transformErrors = function (ValidationException $exception) {
                        $errors = [];

                        foreach ($exception->errors() as $field => $message) {
                            $errors[] = [
                                'field' => $field,
                                'message' => $message[0],
                            ];
                        }

                        return $errors[0]['message'];
                    };

                    return response()->json(['message' => $transformErrors($exception), 'errors' => $exception->errors(), 'status' => 400], 400);
                }
            }
        });
    })
    ->withCommands([])
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('app:cache-t-b-o-hotel-in-database-command')->dailyAt('01:00');
        // queue:work intentionally removed from scheduler — it is not a daemon and exits after
        // draining the queue. Worker management is done by supervisord (docker/supervisord.conf).
        $schedule->command('reservations:reconcile-failed-paid')->everyTenMinutes()->withoutOverlapping();
    })
    ->create();
