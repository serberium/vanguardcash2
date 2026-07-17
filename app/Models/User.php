<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'username', 'password', 'is_sadmin', 'is_admin', 'company_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_sadmin' => 'boolean', 
            'is_admin' => 'boolean',  
        ];
    }

    // --- CARGOS E PERMISSÕES ---

    public function isSadmin(): bool
    {
        return $this->is_sadmin === true;
    }

    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    // O activeRole agora prioriza a tabela de funcionários
    public function getActiveRoleAttribute()
    {
        // Se for admin, não entra na regra de evento
        if ($this->isAdmin()) return null;

        // 1. Busca eventos associados ao usuário através da tabela 'event_user'
        // 2. Filtra eventos onde o status do Event seja 'iniciado'
        // 3. O 'first()' pegará o primeiro evento ativo encontrado
        $event = $this->events()
            ->where('status', 'iniciado')
            ->first();

        // Se encontrou, retorna o papel da tabela 'event_user'
        return $event ? $event->pivot->role : null;
    }

    public function isCaixa(): bool
    {
        return $this->activeRole === 'caixa';
    }

    public function isProducao(): bool
    {
        return $this->activeRole === 'producao';
    }

    // --- RELACIONAMENTOS ---

    public function company(): BelongsTo {
        return $this->belongsTo(Company::class);
    }

    // Relacionamento com funcionário
    public function employee(): HasOne {
        return $this->hasOne(Employee::class);
    }

    // Relacionamento de eventos para ADMINS (tabela event_user)
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_user')
                    ->withPivot('role', 'is_active')
                    ->withTimestamps();
    }

    public function canManageEvent(Event $event): bool
    {
        return $this->is_admin && $this->company_id === $event->company_id;
    }
}