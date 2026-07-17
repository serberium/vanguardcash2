<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    protected $fillable = [
        'name', 
        'role', // Este campo parece ser o cargo fixo do funcionário, não o do evento
        'company_id', 
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relacionamento com Eventos através da tabela pivô 'event_employee'.
     * Adicionamos withPivot para que possamos acessar 'is_active' e 'role' 
     * (assumindo que 'role' é o nome da coluna na tabela pivô).
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_employee')
                    ->withPivot('role', 'is_active')
                    ->withTimestamps();
    }
}