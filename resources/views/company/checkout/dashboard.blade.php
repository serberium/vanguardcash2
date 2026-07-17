<x-app-layout>
    <x-slot name="header">
        <!-- Sistema de Abas -->
        <div class="mb-8 border-b border-gray-200">
            <nav class="flex space-x-8">
                <a href="{{ route('dashboard') }}"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('dashboard') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Resumo
                </a>

                <a href="{{ route('checkout.status') }}"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('checkout.status') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    {{ !$caixaAberto ? 'Abrir Caixa' : 'Fechar Caixa' }}
                </a>

                @if($caixaAberto)
                <a href="{{ route('checkout.withdraw') }}"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('checkout.withdraw') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Sangrar Caixa
                </a>

                <a href="{{ route('checkout.sale') }}"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('checkout.sale') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Venda
                </a>
                @endif
            </nav>
        </div>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-6">Resumo do Caixa - {{ ucfirst($caixa->status) }}</h2>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-gray-100 rounded">Saldo Inicial: <strong>R$ {{ number_format($resumo['abertura'], 2,
                        ',', '.') }}</strong></div>
                <div class="p-4 bg-red-100 rounded">Total Sangrias: <strong>R$ {{ number_format(abs($resumo['sangria']),
                        2, ',', '.') }}</strong></div>
                <div class="p-4 bg-green-100 rounded">Vendas Dinheiro: <strong>R$ {{ number_format($resumo['dinheiro'],
                        2, ',', '.') }}</strong></div>
                <div class="p-4 bg-blue-100 rounded">Vendas PIX: <strong>R$ {{ number_format($resumo['pix'], 2, ',',
                        '.') }}</strong></div>
                <div class="p-4 bg-purple-100 rounded">Vendas Cartão: <strong>R$ {{ number_format($resumo['cartao'], 2,
                        ',', '.') }}</strong></div>
                <!-- Novo card de Consumo Interno -->
                <div class="p-4 bg-yellow-100 rounded">Consumo Interno: <strong>R$ {{ number_format($resumo['interno'],
                        2, ',', '.') }}</strong></div>
            </div>

            @if($caixa->status === 'fechado')
            @php
            $totalVendas = $resumo['dinheiro'] + $resumo['pix'] + $resumo['cartao'];
            $totalSangrias = abs($resumo['sangria']);
            $saldoEsperado = ($resumo['abertura'] + $totalVendas) - $totalSangrias;
            $diferenca = $caixa->closing_amount - $saldoEsperado;
            @endphp

            <div class="mt-8 p-6 bg-gray-50 rounded border-t-4 border-gray-800">
                <h3 class="text-xl font-bold mb-4">Conferência de Fechamento</h3>
                <div class="space-y-2">
                    <p>Saldo Esperado (Abertura + Vendas - Sangrias):
                        <strong>R$ {{ number_format($saldoEsperado, 2, ',', '.') }}</strong>
                    </p>
                    <p>Valor Declarado:
                        <strong>R$ {{ number_format($caixa->closing_amount, 2, ',', '.') }}</strong>
                    </p>

                    <div
                        class="mt-4 p-4 rounded text-white font-bold text-center {{ abs($diferenca) < 0.01 ? 'bg-green-600' : 'bg-red-600' }}">
                        @if(abs($diferenca) < 0.01) CAIXA FECHADO COM SUCESSO (Sem diferença) @else DIFERENÇA: R$ {{
                            number_format($diferenca, 2, ',' , '.' ) }} ({{ $diferenca> 0 ? 'SOBRA' : 'QUEBRA' }})
                            @endif
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-4">Fechado em: {{ $caixa->closed_at }}</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>