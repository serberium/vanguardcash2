<x-app-layout>
    <div class="py-12 max-w-4xl mx-auto">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-6">Relatório: {{ $event->name }}</h2>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-green-100 rounded">Dinheiro: R$ {{ number_format($report['total_dinheiro'], 2, ',',
                    '.') }}</div>
                <div class="p-4 bg-blue-100 rounded">PIX: R$ {{ number_format($report['total_pix'], 2, ',', '.') }}
                </div>
                <div class="p-4 bg-purple-100 rounded">Cartão: R$ {{ number_format($report['total_cartao'], 2, ',', '.')
                    }}</div>
                <div class="p-4 bg-yellow-100 rounded">Consumo Interno: R$ {{ number_format($report['consumo_interno'],
                    2, ',', '.') }}</div>
                <div class="p-4 bg-gray-800 text-white rounded col-span-2 text-center text-xl font-bold">
                    TOTAL GERAL: R$ {{ number_format($report['total_geral'], 2, ',', '.') }}
                </div>
            </div>

            <a href="{{ route('dashboard') }}" class="mt-6 block text-blue-600 underline">Voltar para o Dashboard</a>
        </div>
    </div>
</x-app-layout>