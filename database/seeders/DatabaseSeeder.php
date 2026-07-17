<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Criar a Empresa
        $company = Company::create([
            'name' => 'Camargo e Correa',
            'domain' => 'camargoecorrea',
            'expires_at' => now()->addDays(30),
            'is_active'  => true,
        ]);

        // 2. Criar o Super Admin (SAdmin)
        User::create([
            'name' => 'Super Admin',
            'username' => 'root_admin',
            'email' => 'sauloscamargo@gmail.com',
            'password' => bcrypt('shaman13'),
            'is_sadmin' => true,
            'is_admin' => true, // Geralmente SAdmin também é Admin
            'company_id' => null,
        ]);

        // 3. Criar o Admin da Empresa
        User::create([
            'name' => 'Admin ' . $company->name,
            'username' => 'admin@' . $company->domain,
            'password' => Hash::make('admin123'),
            'company_id' => $company->id,
            'is_sadmin' => false,
            'is_admin' => true,
        ]);

        // 4. Criar um evento inicial para teste
        $event = Event::create([
            'company_id' => $company->id,
            'name'       => 'Evento de Teste Inicial',
            'start_date' => now(),                 // Data de início agora
            'end_date'   => now()->addDays(7),     // Data de fim daqui a 7 dias
            'status'     => 'criado',
            'is_active'  => true,
        ]);

        // 5. Criar funcionários (Caixa e Produção) e vincular à SuperPivô
        $roles = ['caixa', 'producao'];
        
        foreach ($roles as $role) {
            $user = User::create([
                'name' => ucfirst($role) . ' ' . $company->name,
                'username' => $role . '@' . $company->domain,
                'password' => Hash::make($role . '123'),
                'company_id' => $company->id,
                'is_sadmin' => false,
                'is_admin' => false,
            ]);

            // Vincula o usuário ao evento com sua função específica
            $user->events()->attach($event->id, [
                'role' => $role,
                'is_active' => true
            ]);
        }
    }
}