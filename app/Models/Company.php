<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    // Define quais campos podem ser preenchidos via código
    protected $fillable = [
        'name',
        'domain',
        'expires_at',
        'is_active',
    ];

    // Transforma a data automaticamente em um objeto Carbon para facilitar cálculos
    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relacionamento para saber quais usuários pertencem a esta empresa
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
