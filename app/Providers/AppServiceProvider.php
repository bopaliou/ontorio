<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Rate Limiter for System Migration (Task 1.1)
        \Illuminate\Support\Facades\RateLimiter::for('strict-migration', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perHour(5)->by($request->ip());
        });

        // Rate Limiter for Sensitive Stats (Task 1.2)
        \Illuminate\Support\Facades\RateLimiter::for('moderate-stats', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        // Global Mutation Rate Limiter (Task 1.3)
        \Illuminate\Support\Facades\RateLimiter::for('global-mutations', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(50)->by($request->user()?->id ?: $request->ip());
        });

        // Dashboard Cache Invalidation (Task 2.2)
        \App\Models\Bien::observe(\App\Observers\DashboardStatsObserver::class);
        \App\Models\Contrat::observe(\App\Observers\DashboardStatsObserver::class);
        \App\Models\Depense::observe(\App\Observers\DashboardStatsObserver::class);
        \App\Models\Loyer::observe(\App\Observers\DashboardStatsObserver::class);
        \App\Models\Paiement::observe(\App\Observers\DashboardStatsObserver::class);

        // Slow Query Logging (Task 2.4)
        if (config('app.debug')) {
            \Illuminate\Support\Facades\DB::listen(function ($query) {
                if ($query->time > 100) { // 100ms
                    \Illuminate\Support\Facades\Log::warning('Slow Query Detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time,
                        'url' => request()->fullUrl(),
                    ]);
                }
            });
        }
    }
}
