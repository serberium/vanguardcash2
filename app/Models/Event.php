<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Event extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'start_date',
        'end_date',
        'status',
        'is_active'
    ];

    /**
     * Define o formato para tratamento automático de datas.
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'is_active'  => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')
                    ->withPivot('role', 'is_active')
                    ->withTimestamps();
    }

    public function canStart(): bool
    {
        $now = Carbon::now();
        
        $dateOk = $now->between($this->start_date, $this->end_date);
        $hasProduct = Product::where('company_id', $this->company_id)->exists();
        $hasCaixa = $this->users()->wherePivot('role', 'caixa')->exists();
        $hasProd = $this->users()->wherePivot('role', 'producao')->exists();

        return $dateOk && $hasProduct && $hasCaixa && $hasProd;
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'event_employee')
                    ->withPivot('role_in_event')
                    ->withTimestamps();
    }

    /**
     * Relacionamento com Produtos (Tabela Pivot event_product)
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'event_product')
                    ->withPivot('price_in_event')
                    ->withTimestamps();
    }

    /**
     * Relacionamento com Caixas
     */
    public function cashRegisters(): HasMany
    {
        return $this->hasMany(CashRegister::class);
    }

    /**
     * Relacionamento com a Empresa
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}