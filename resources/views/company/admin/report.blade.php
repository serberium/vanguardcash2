<x-app-layout>
    <div class="py-12 max-w-6xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Relatórios do Evento: {{ $event->name }}</h1>

        <!-- Filtro -->
        <form method="GET" class="bg-white p-4 rounded shadow mb-6 flex gap-4 items-end">
            <div>
                <label class="block text-sm font-medium">Data Início</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="border rounded p-2">
            </div>
            <div>
                <label class="block text-sm font-medium">Data Fim</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="border rounded p-2">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filtrar</button>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
            $relatorios = [
            'Diário' => $dailySales,
            'Período Selecionado' => $periodSales,
            'Total do Evento' => $eventSales
            ];
            @endphp

            @foreach($relatorios as $titulo => $vendas)
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-xl font-bold mb-4">{{ $titulo }}</h2>

                <div class="mb-4">
                    @foreach($vendas->groupBy('payment_method') as $method => $group)
                    <div class="flex justify-between border-b py-1">
                        <span>{{ ucfirst($method) }}</span>
                        <strong>R$ {{ number_format($group->sum('total_amount'), 2, ',', '.') }}</strong>
                    </div>
                    @endforeach
                </div>

                <h3 class="font-bold text-sm text-gray-500 mb-2">Vendas por Produto</h3>
                <div class="max-h-64 overflow-y-auto">
                    @foreach($vendas->flatMap->items->groupBy('product.name') as $name => $items)
                    <div class="text-sm py-1 flex justify-between">
                        <span>{{ $name }} ({{ $items->sum('quantity') }})</span>
                        <span>R$ {{ number_format($items->sum('total_price'), 2, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-app-layout>