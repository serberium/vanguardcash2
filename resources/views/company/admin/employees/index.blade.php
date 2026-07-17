<x-app-layout>
    <x-slot name="header">
        <div class="mb-8 border-b border-gray-200">
            <nav class="flex space-x-8">
                <a href="{{ route('dashboard') }}"
                    class="py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700">Gerenciar
                    Eventos</a>
                <a href="{{ route('company.employees') }}"
                    class="py-4 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600">Gerenciar Func.</a>
            </nav>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm mb-8 border border-gray-200">
                <h3 id="form-title" class="font-bold text-lg mb-6 text-gray-800">Cadastrar Novo Funcionário</h3>

                <form id="employee-form" action="{{ route('company.employees.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="method-field" name="_method" value="POST">

                    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.5rem;"
                        class="mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                            <input type="text" id="name" name="name" class="w-full border-gray-300 rounded shadow-sm"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" id="username" name="username"
                                class="w-full border-gray-300 rounded shadow-sm" placeholder="ex: joao" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                            <select id="role" name="role" class="w-full border-gray-300 rounded shadow-sm" required>
                                @foreach(\App\Enums\EmployeeRole::cases() as $role)
                                <option value="{{ $role->value }}">{{ $role->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-center gap-4">
                        <button type="submit" id="btn-submit"
                            class="bg-blue-600 text-white px-8 py-2 rounded font-bold hover:bg-blue-700">Salvar
                            Funcionário</button>
                        <button type="button" id="btn-cancel" onclick="resetForm()"
                            class="hidden bg-gray-500 text-white px-8 py-2 rounded font-bold hover:bg-gray-600">Cancelar</button>
                    </div>
                </form>
            </div>

            <div class="flex flex-wrap gap-4">
                @forelse($employees as $employee)
                <div
                    class="w-56 bg-white border border-gray-200 rounded shadow-sm p-4 flex flex-col items-center text-center">
                    <h4 class="font-bold text-gray-800">{{ $employee->name }}</h4>
                    <p class="text-xs text-gray-500 mb-4 uppercase">{{ $employee->role }}</p>

                    <div class="flex gap-2">
                        <button type="button"
                            onclick="editEmployee('{{ $employee->id }}', '{{ $employee->name }}', '{{ $employee->role }}', '{{ $employee->user->username ?? '' }}')"
                            class="bg-blue-500 text-white px-4 py-1 rounded text-xs hover:bg-blue-600">Editar</button>

                        <form action="{{ route('company.employees.destroy', $employee->id) }}" method="POST"
                            onsubmit="return confirm('Excluir este funcionário?')">
                            @csrf @method('DELETE')
                            <button
                                class="bg-red-500 text-white px-4 py-1 rounded text-xs hover:bg-red-600">Excluir</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-gray-500">Nenhum funcionário cadastrado.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        function editEmployee(id, name, role, fullUsername) {
            document.getElementById('form-title').innerText = 'Editando: ' + name;
            // CORREÇÃO: Usando a URL correta definida no web.php
            document.getElementById('employee-form').action = '/funcionarios/' + id; 
            document.getElementById('method-field').value = 'PUT';
            document.getElementById('name').value = name;
            document.getElementById('role').value = role;
            
            const usernamePart = fullUsername ? fullUsername.split('@')[0] : '';
            document.getElementById('username').value = usernamePart;
            document.getElementById('username').disabled = true; 
            
            document.getElementById('btn-submit').innerText = 'Atualizar Funcionário';
            document.getElementById('btn-cancel').classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function resetForm() {
            document.getElementById('form-title').innerText = 'Cadastrar Novo Funcionário';
            document.getElementById('employee-form').action = '{{ route("company.employees.store") }}';
            document.getElementById('method-field').value = 'POST';
            document.getElementById('username').disabled = false;
            document.getElementById('employee-form').reset();
            document.getElementById('btn-submit').innerText = 'Salvar Funcionário';
            document.getElementById('btn-cancel').classList.add('hidden');
        }
    </script>
</x-app-layout>