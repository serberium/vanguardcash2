<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit_price',
        'package_size',
        'servings_per_unit',
        'production_stock',
        'sale_stock',
        'low_stock_threshold',
        'critical_stock_threshold',
        'company_id'
    ];

    /**
     * Relacionamento com a Empresa
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relacionamento com Eventos
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_product')
                    ->withPivot('price_in_event')
                    ->withTimestamps();
    }

    /**
     * Relacionamento com Itens de Venda
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Finaliza a produção: transfere de 'production_stock' para 'sale_stock'
     */
    public function finalizeProduction(int $quantity): bool
    {
        if ($this->production_stock >= $quantity) {
            $this->decrement('production_stock', $quantity);
            $this->increment('sale_stock', $quantity);
            return true;
        }
        return false;
    }

    /**
     * Registra venda: subtrai do 'sale_stock'
     * Agora o método recebe o valor total já calculado pelo Controller
     */
    public function sell(int $totalUnits): bool
    {
        if ($this->sale_stock >= $totalUnits) {
            $this->decrement('sale_stock', $totalUnits);
            return true;
        }
        return false;
    }
}