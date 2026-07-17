<?php

namespace App\Http\Controllers\Company\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User; // Importação necessária
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::where('company_id', auth()->user()->company_id)->get();
        return view('company.admin.employees.index', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50',
            'role'     => 'required|in:caixa,producao',
        ]);

        // 1. Busca o domain da empresa logada
        $domain = auth()->user()->company->domain; 
        
        // 2. Constrói o username seguindo o padrão: nomeEscolhido@domain
        $fullUsername = $request->username . '@' . $domain;
        
        // 3. Define a senha conforme regra: role + 123
        $password = $request->role . '123';

        // 4. Cria o usuário
        $user = User::create([
            'name'       => $request->name,
            'username'   => $fullUsername,
            'password'   => Hash::make($password),
            'company_id' => auth()->user()->company_id,
            'is_admin'   => false,
        ]);

        // 5. Cria o funcionário vinculado
        Employee::create([
            'name'       => $request->name,
            'role'       => $request->role,
            'user_id'    => $user->id,
            'company_id' => auth()->user()->company_id,
        ]);

        return back()->with('success', "Funcionário criado! Acesso: $fullUsername | Senha: $password");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:caixa,producao',
        ]);

        $employee = Employee::where('company_id', auth()->user()->company_id)->findOrFail($id);
        
        // Atualiza o Funcionário
        $employee->update([
            'name' => $request->name,
            'role' => $request->role,
        ]);

        // Atualiza o nome do Usuário (mantendo o username original intacto no banco)
        if ($employee->user) {
            $employee->user->update([
                'name' => $request->name,
            ]);
        }

        return back()->with('success', 'Funcionário atualizado com sucesso!');
    }

    public function destroy($id)
    {
        // 1. Busca o funcionário primeiro para garantir que ele pertence à empresa logada
        $employee = Employee::where('company_id', auth()->user()->company_id)->findOrFail($id);

        // 2. Se houver um user_id associado, exclui o usuário
        if ($employee->user_id) {
            $user = \App\Models\User::find($employee->user_id);
            if ($user) {
                $user->delete();
            }
        }

        // 3. Exclui o registro do funcionário
        $employee->delete();

        return back()->with('success', 'Funcionário e usuário removidos com sucesso!');
    }
}