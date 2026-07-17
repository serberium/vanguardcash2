<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Produtos do Evento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow">
                <table class="w-full">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="p-2">Produto</th>
                            <th class="p-2">Em Produção</th>
                            <th class="p-2">Pronto Venda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr class="border-b">
                            <td class="p-2 font-bold">{{ $product->name }}</td>

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