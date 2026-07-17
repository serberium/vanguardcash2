<x-app-layout>
    <x-slot name="header">
        <!-- Sistema de Abas -->
        <div class="mb-8 border-b border-gray-200">
            <nav class="flex space-x-8">
                <!-- Resumo -->
                <a href="{{ route('dashboard') }}"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('dashboard') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Resumo
                </a>

                <!-- Abertura/Fechamento -->
                <a href="{{ route('checkout.status') }}"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('checkout.status') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    {{ !$caixaAberto ? 'Abrir Caixa' : 'Fechar Caixa' }}
                </a>

                @if($caixaAberto)
                <!-- Sangria -->
                <a href="{{ route('checkout.withdraw') }}"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('checkout.withdraw') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Sangrar Caixa
                </a>

                <!-- Venda -->
                <a href="{{ route('checkout.sale') }}"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('checkout.sale') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Venda
                </a>
                @endif
            </nav>
        </div>
    </x-slot>

    <div class="py-12 max-w-lg mx-auto">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold mb-4 text-red-600">Realizar Sangria</h2>
            <p class="text-sm text-gray-600 mb-6">
                A sangria retira dinheiro do caixa para depósito ou segurança. Este valor será registrado no sistema.
            </p>

            <form action="{{ route('checkout.withdraw.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block mb-2 font-medium text-gray-700">Valor da Sangria (R$):</label>
                    <input type="number" step="0.01" name="amount" class="w-full border rounded p-3 text-lg"
                        placeholder="0,00" required>
                </div>

                <button type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded font-bold transition">
                    CONFIRMAR SANGRIA
                </button>
            </form>
        </div>
    </div>
</x-app-layout>