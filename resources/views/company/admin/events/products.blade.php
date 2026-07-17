<x-app-layout>
    <x-slot name="header">
        <div class="mb-8 border-b border-gray-200">
            <nav class="flex space-x-8">
                <a href="{{ route('dashboard') }}"
                    class="py-4 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600">Gerenciar Eventos</a>
                <a href="{{ route('company.employees') }}"
                    class="py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700">Gerenciar
                    Func.</a>
            </nav>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-xl font-bold mb-6">Produtos do Evento</h2>

            <div class="bg-white p-6 rounded shadow mb-8">
                <h3 class="font-bold mb-4">Cadastrar Novo Produto</h3>
                <form action="{{ route('company.events.products.store', $event_id) }}" method="POST"
                    class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    @csrf
                    <input type="text" name="name" placeholder="Nome" class="border rounded p-2" required>
                    <input type="number" step="0.01" name="unit_price" placeholder="Preço" class="border rounded p-2"
                        required>
                    <input type="number" name="package_size" placeholder="Tam. Porção" class="border rounded p-2"
                        required>
                    <input type="number" name="low_stock_threshold" placeholder="Alerta Baixo"
                        class="border rounded p-2" required>
                    <input type="number" name="critical_stock_threshold" placeholder="Alerta Crítico"
                        class="border rounded p-2" required>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Cadastrar</button>
                </form>
            </div>

            <div class="bg-white p-6 rounded shadow">
                <table class="w-full">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="p-2">Produto</th>
                            <th class="p-2">Configurações (Preço/Porção)</th>
                            <th class="p-2">Em Produção</th>
                            <th class="p-2">Pronto Venda</th>
                            <th class="p-2">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr class="border-b">
                            <td class="p-2 font-bold">{{ $product->name }}</td>
                            <td class="p-2">
                                <form
                                    action="{{ route('company.events.products.update', ['id' => $product->id, 'event_id' => $event_id]) }}"
                                    method="POST" class="flex gap-1">
                                    @csrf @method('PUT')
                                    <input type="number" step="0.01" name="unit_price"
                                        value="{{ $product->unit_price }}" class="w-20 border rounded p-1" {{
                                        $product->sale_stock > 0 ? 'disabled' : '' }}>
                                    <input type="number" name="package_size" value="{{ $product->package_size }}"
                                        class="w-16 border rounded p-1" {{ $product->sale_stock > 0 ? 'disabled' : ''
                                    }}>
                                    @if($product->sale_stock == 0)
                                    <button type="submit"
                                        class="text-xs bg-blue-500 text-white px-2 rounded">OK</button>
                                    @endif
                                </form>
                            </td>

                            <!-- AQUI ESTÁ A MUDANÇA PARA FICAR EM UMA LINHA -->
                            <td class="p-2">
                                <form action="{{ route('company.events.products.add-production', $product->id) }}"
                                    method="POST" class="flex items-center gap-2">
                                    @csrf
                                    <span class="font-bold">{{ $product->production_stock }}</span>
                                    <input type="number" name="quantity" class="w-16 border rounded p-1"
                                        placeholder="Qtd" required>
                                    <button type="submit"
                                        class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">+</button>
                                </form>
                            </td>

                            <td class="p-2">
                                <form action="{{ route('company.events.products.finalize-production', $product->id) }}"
                                    method="POST" class="flex items-center gap-2">
                                    @csrf
                                    <span class="font-bold">{{ $product->sale_stock }}</span>
                                    <input type="number" name="quantity" class="w-16 border rounded p-1"
                                        placeholder="Qtd" required>
                                    <button type="submit"
                                        class="bg-blue-600 text-white px-2 py-1 rounded text-xs">Liberar</button>
                                </form>
                            </td>

                            <td class="p-2">
                                <form
                                    action="{{ route('company.events.products.destroy', ['id' => $product->id, 'event_id' => $event_id]) }}"
                                    method="POST" onsubmit="return confirm('Excluir?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 font-bold text-sm">Excluir</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500">Nenhum produto cadastrado para este
                                evento.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>