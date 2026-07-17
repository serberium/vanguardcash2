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
            @if(!$caixaAberto)
            <!-- FORMULÁRIO DE ABERTURA -->
            <h2 class="text-xl font-bold mb-4">Abrir Caixa</h2>
            <form action="{{ route('checkout.open') }}" method="POST">
                @csrf
                <input type="hidden" name="event_id" value="{{ $activeEvent->id }}">
                <label class="block mb-2">Valor de Abertura (Troco):</label>
                <input type="number" step="0.01" name="opening_amount" class="w-full border rounded p-2 mb-4" required>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Abrir Caixa</button>
            </form>
            @else
            <!-- FORMULÁRIO DE FECHAMENTO -->
            <h2 class="text-xl font-bold mb-4">Fechar Caixa</h2>
            <p class="mb-4 text-gray-600">Aberto em: {{ $caixaAberto->opened_at }}</p>
            <form action="{{ route('checkout.close') }}" method="POST">
                @csrf
                <label class="block mb-2">Valor Total em Caixa (Final):</label>
                <input type="number" step="0.01" name="closing_amount" class="w-full border rounded p-2 mb-4" required>
                <button type="submit" class="w-full bg-red-600 text-white py-2 rounded">Fechar Caixa</button>
            </form>
            @endif
        </div>
    </div>
</x-app-layout>