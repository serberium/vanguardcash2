<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompanyService
{
    public function createCompanyWithAdmin(array $data): Company
    {
        return DB::transaction(function () use ($data) {
            // 1. Cria a Empresa
            $company = Company::create([
                'name' => $data['name'],
                'domain' => $data['domain'],
            ]);

            // 2. Cria o Admin da Empresa
            User::create([
                'name' => 'Admin ' . $data['name'],
                'username' => 'admin@' . $data['domain'],
                'email' => null, // Opcional, se não for usar agora
                'password' => Hash::make($data['name']), // Senha padrão = nome da empresa
                'company_id' => $company->id,
                'is_sadmin' => false,
                'is_admin' => true,
            ]);

            return $company;
        });
    }
}