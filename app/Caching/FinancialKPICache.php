<?php

namespace App\Caching;

use Illuminate\Support\Facades\Cache;

/**
 * Service de caching pour les statistiques financières
 * 
 * Les KPIs changent rarement pendant le mois, donc on peut cacher
 * les calculs complexes sans problème de fra îcheur
 */
class FinancialKPICache
{
    private const CACHE_PREFIX = 'financial_kpis_';
    private const CACHE_DURATION = 3600; // 1 heure

    /**
     * Obtenir KPIs du cache, ou calculer et cacher
     * 
     * @param string $mois Format Y-m (ex: 2026-02)
     * @param callable $callback Fonction pour calculer si pas en cache
     * @return array
     */
    public static function getOrCalculate(string $mois, callable $callback): array
    {
        $key = self::CACHE_PREFIX . $mois;
        
        return Cache::remember(
            $key,
            self::CACHE_DURATION,
            $callback
        );
    }

    /**
     * Invalider cache pour un mois
     * 
     * @param string $mois Format Y-m
     */
    public static function invalidate(string $mois): void
    {
        Cache::forget(self::CACHE_PREFIX . $mois);
    }

    /**
     * Invalider tous les caches KPI
     */
    public static function flushAll(): void
    {
        Cache::flush();
    }

    /**
     * Invalider cache du mois courant
     */
    public static function invalidateCurrent(): void
    {
        self::invalidate(now()->format('Y-m'));
    }
}
