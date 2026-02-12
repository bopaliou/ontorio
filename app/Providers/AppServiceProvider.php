<?php

namespace App\Providers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Depense;
use App\Models\Loyer;
use App\Models\Paiement;
use App\Observers\DashboardStatsObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
            URL::forceScheme('https');
        }

        RateLimiter::for('strict-migration', function (Request $request) {
            return Limit::perHour(5)->by($request->ip());
        });

        RateLimiter::for('moderate-stats', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('global-mutations', function (Request $request) {
            return Limit::perMinute(50)->by($request->user()?->id ?: $request->ip());
        });

        // Les permissions sont gérées par Spatie Laravel Permission (HasRoles sur User).
        // Il n'est pas nécessaire de redéfinir des Gates récursives ici.

        Bien::observe(DashboardStatsObserver::class);
        Contrat::observe(DashboardStatsObserver::class);
        Depense::observe(DashboardStatsObserver::class);
        Loyer::observe(DashboardStatsObserver::class);
        Paiement::observe(DashboardStatsObserver::class);

        if (config('app.debug')) {
            DB::listen(function ($query) {
                if ($query->time > 100) {
                    Log::warning('Slow Query Detected', [
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
