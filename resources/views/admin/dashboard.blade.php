<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gerenciamento de Empresas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- FORMULÁRIO DE CADASTRO -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg mb-6">
                <h3 class="text-lg font-medium mb-4">Cadastrar Nova Empresa</h3>
                <form action="{{ route('admin.companies.store') }}" method="POST" class="flex gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium">Nome</label>
                        <input type="text" name="name" class="border rounded p-2 w-full" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Domínio</label>
                        <input type="text" name="domain" class="border rounded p-2 w-full" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Data de Vencimento</label>
                        <input type="date" name="expires_at" class="border rounded p-2 w-full" required>
                    </div>
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Cadastrar</button>
                </form>
            </div>

            <!-- TABELA DE EMPRESAS -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4">Empresa</th>
                            <th class="p-4">Vencimento</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($companies as $company)
                        <tr class="border-b">
                            <td class="p-4">{{ $company->name }}</td>
                            <td class="p-4">{{ $company->expires_at?->format('d/m/Y') }}</td>
                            <td class="p-4">
                                <span
                                    class="px-2 py-1 rounded text-sm {{ $company->expires_at?->addDays(3)->isPast() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $company->expires_at?->addDays(3)->isPast() ? 'Expirada' : 'Ativa' }}
                                </span>
                            </td>
                            <td class="p-4 flex gap-2">
                                <!-- Formulário de renovação -->
                                <form action="{{ route('admin.companies.renew', $company->id) }}" method="POST"
                                    class="flex gap-2 items-center">
                                    @csrf @method('PUT')
                                    <input type="date" name="expires_at" class="border rounded p-1 text-sm" required>
                                    <button type="submit"
                                        class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                        Renovar
                                    </button>
                                </form>

                                <!-- Botão de exclusão -->
                                <form action="{{ route('admin.companies.destroy', $company->id) }}" method="POST"
                                    onsubmit="return confirm('ATENÇÃO: Tem certeza que deseja excluir a empresa {{ $company->name }} e TODOS os seus dados? Esta ação é irreversível.');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>