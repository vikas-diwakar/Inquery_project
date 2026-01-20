<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasTenant
{
    /**
     * Boot the trait
     */
    public static function bootHasTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && auth()->user()->company_id) {
                $builder->where('company_id', auth()->user()->company_id);
            }
        });
    }

    /**
     * Scope a query to only include records for a specific tenant
     */
    public function scopeForTenant(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }
}
