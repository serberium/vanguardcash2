<?php

namespace App\Enums;

enum EmployeeRole: string
{
    case CAIXA = 'caixa';
    case PRODUCAO = 'producao';

    public function label(): string
    {
        return match($this) {
            self::CAIXA => 'Caixa',
            self::PRODUCAO => 'Produção',
        };
    }
}