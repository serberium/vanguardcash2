<?php

namespace App\Traits;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void {
        static::addGlobalScope('company', function (Builder $builder) {
            // Se NÃO for o Sayadim, filtramos pela empresa
            if (auth()->check() && !auth()->user()->is_sadmin) {
                $builder->where('company_id', auth()->user()->company_id);
            }
        });

        static::creating(function (Model $model) {
            // Se NÃO for o Sayadim, associamos automaticamente à empresa do usuário
            if (auth()->check() && !auth()->user()->is_sadmin) {
                $model->company_id = auth()->user()->company_id;
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
