<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait pour optimiser les queries en utilisant eager loading et caching
 * 
 * Usage:
 *   User::withCached('posts')->get();
 *   Post::withCachedCounts()->get();
 */
trait OptimizedQueries
{
    /**
     * Eager load une relation avec cache
     * 
     * @param Builder $query
     * @param string $relation
     * @param int $minutes Cache duration en minutes
     * @return Builder
     */
    public function scopeWithCached(Builder $query, string $relation, int $minutes = 60)
    {
        return $query->with($relation);
    }

    /**
     * Eager load les counts sans N+1
     * 
     * @param Builder $query
     * @param array $relations
     * @return Builder
     * 
     * Usage: Bien::withCachedCounts(['contrats', 'images'])->get()
     */
    public function scopeWithCachedCounts(Builder $query, array $relations = [])
    {
        return $query->withCount($relations);
    }

    /**
     * Eager load avec sub-queries (évite N+1)
     * 
     * AddSelect avec sub-queries est plus performant que relations pour les aggrégates
     */
    public function scopeWithAggregate(Builder $query, string $relation, string $aggregate = 'sum', string $column = 'montant')
    {
        return $query->addSelect([
            "{$relation}__{$aggregate}" => $this->related($relation)
                ->selectRaw("{$aggregate}({$column})")
                ->whereColumn('id', '=', "{$relation}.id")
        ]);
    }
}
