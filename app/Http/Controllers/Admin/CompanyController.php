<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'domain'     => 'required|string|unique:companies,domain|max:100',
            'expires_at' => 'required|date',
        ]);

        DB::transaction(function () use ($validated) {
            // Cria a Empresa apenas aqui, uma única vez
            $company = Company::create(array_merge($validated, ['is_active' => true]));

            // Cria o Admin padrão da nova empresa usando a variável $company acima
            User::create([
                'name'       => 'Admin ' . $company->name,
                'username'   => 'admin@' . $company->domain,
                'password'   => Hash::make('admin123'),
                'company_id' => $company->id,
                'is_sadmin'  => false,
                'is_admin'   => true,
                'role'       => 'admin',
            ]);
        });

        return back()->with('success', 'Empresa criada com sucesso!');
    }

    public function renew(Request $request, $id)
    {
        // 1. Valida se a data foi enviada
        $request->validate([
            'expires_at' => 'required|date',
        ]);

        // 2. Busca a empresa pelo ID
        $company = \App\Models\Company::findOrFail($id);

        // 3. Atualiza a data e garante que ela esteja ativa
        $company->update([
            'expires_at' => $request->expires_at,
            'is_active'  => true, 
        ]);

        // 4. Retorna com uma mensagem de sucesso
        return back()->with('success', 'Licença renovada com sucesso!');
    }

    public function destroy($id)
    {
        $company = \App\Models\Company::findOrFail($id);
        
        // Isso disparará a exclusão em cascata definida no banco
        $company->delete();

        return back()->with('success', 'Empresa e todos os seus dados removidos com sucesso!');
    }

}