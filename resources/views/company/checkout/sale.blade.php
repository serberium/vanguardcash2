<x-app-layout>
    <x-slot name="header">
        <!-- Sistema de Abas -->
        <div class="mb-0 border-b border-gray-200">
            <nav class="flex space-x-8">
                <a href="{{ route('dashboard') }}"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('dashboard') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">Resumo</a>
                <a href="{{ route('checkout.status') }}"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('checkout.status') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">{{
                    !$caixaAberto ? 'Abrir Caixa' : 'Fechar Caixa' }}</a>
                @if($caixaAberto)
                <a href="{{ route('checkout.withdraw') }}"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('checkout.withdraw') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">Sangrar
                    Caixa</a>
                <a href="{{ route('checkout.sale') }}"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('checkout.sale') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">Venda</a>
                @endif
            </nav>
        </div>
    </x-slot>

    <!-- Header Fixo (Total) -->
    <div class="fixed top-30 m-2 left-0 right-0 bg-white shadow-md p-4 flex justify-between items-center z-40 border-b">
        <h2 class="text-xl font-bold ml-4">Total: R$ <span id="total-display">0,00</span></h2>
        <div class="mr-4">
            <button type="button" onclick="resetForm()" class="bg-gray-500 text-white px-4 py-2 rounded">Reset</button>
            <button type="button" onclick="openModal()" class="bg-green-700 text-white px-4 py-2 rounded ml-2">Fechar
                Pedido</button>
        </div>
    </div>

    <!-- Grid de Produtos -->
    <div class="pt-24 p-4 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($products as $product)
        @php
        $bgClass = 'bg-green-300';
        if ($product->sale_stock <= $product->critical_stock_threshold) $bgClass = 'bg-red-400';
            elseif ($product->sale_stock <= $product->low_stock_threshold) $bgClass = 'bg-yellow-400';
                @endphp
                <div class="{{ $bgClass }} p-4 rounded shadow border border-gray-200 product-card"
                    data-price="{{ $product->unit_price }}" data-id="{{ $product->id }}">
                    <h3 class="font-bold text-center text-sm">{{ $product->name }}</h3>
                    <p class="text-center font-bold">R$ {{ number_format($product->unit_price, 2, ',', '.') }}</p>
                    <input type="number" value="0" min="0" max="{{ $product->sale_stock }}"
                        class="qty-input w-full mt-2 border rounded p-1 text-center" oninput="calculateTotal()">
                    <p class="text-[10px] text-center mt-1">Estoque: {{ $product->sale_stock }}</p>
                </div>
                @endforeach
    </div>

    <!-- Modal de Pagamento -->
    <div id="payment-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-sm">
            <form action="{{ route('checkout.sale.store') }}" method="POST">
                @csrf
                <input type="hidden" name="cash_register_id" value="{{ $caixaAberto->id }}">
                <div id="modal-items-container"></div>

                <h3 class="text-center text-xl font-bold mb-4">Total do Pedido: R$ <span id="modal-total">0,00</span>
                </h3>

                <select name="payment_method" id="payment_method" class="w-full border rounded p-2 mb-4"
                    onchange="toggleReceivedField()" required>
                    <option value="">Forma de pagamento...</option>
                    <option value="dinheiro">Dinheiro</option>
                    <option value="pix">PIX</option>
                    <option value="cartao">Cartão</option>
                    <option value="consumo_interno">Consumo Interno</option>
                </select>

                <div id="received-field-wrapper">
                    <input type="number" step="0.01" name="received_amount" id="received_amount"
                        placeholder="Valor Recebido" class="w-full border rounded p-2 mb-4" oninput="calculateChange()">
                    <p class="mb-4 font-bold text-red-600 text-lg">Troco: R$ <span id="change-display">0,00</span></p>
                </div>

                <button type="submit"
                    class="w-full bg-green-600 text-white py-2 mb-2 font-bold hover:bg-green-700 rounded">Confirmar
                    Venda</button>
                <button type="button" onclick="closeModal()"
                    class="w-full bg-gray-600 text-white py-2 hover:bg-gray-700 rounded">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.product-card').forEach(card => {
                let qty = parseInt(card.querySelector('.qty-input').value) || 0;
                let price = parseFloat(card.dataset.price);
                total += (qty * price);
            });
            document.getElementById('total-display').innerText = total.toFixed(2).replace('.', ',');
            document.getElementById('modal-total').innerText = total.toFixed(2).replace('.', ',');
            calculateChange(); // Atualiza o troco se mudar o total
        }

        function toggleReceivedField() {
            const method = document.getElementById('payment_method').value;
            const wrapper = document.getElementById('received-field-wrapper');
            if (method === 'dinheiro') {
                wrapper.style.display = 'block';
            } else {
                wrapper.style.display = 'none';
            }
        }

        function openModal() {
            let total = parseFloat(document.getElementById('total-display').innerText.replace(',', '.'));
            if (total <= 0) {
                alert("Adicione produtos ao pedido!");
                return;
            }
            let container = document.getElementById('modal-items-container');
            container.innerHTML = '';
            document.querySelectorAll('.product-card').forEach(card => {
                let qty = card.querySelector('.qty-input').value;
                if(qty > 0) {
                    container.innerHTML += `<input type="hidden" name="items[${card.dataset.id}]" value="${qty}">`;
                }
            });
            toggleReceivedField(); // Garante o estado correto ao abrir
            document.getElementById('payment-modal').classList.remove('hidden');
        }

        function closeModal() { document.getElementById('payment-modal').classList.add('hidden'); }

        function calculateChange() {
            let total = parseFloat(document.getElementById('modal-total').innerText.replace(',', '.'));
            let received = parseFloat(document.getElementById('received_amount').value || 0);
            document.getElementById('change-display').innerText = (received - total).toFixed(2).replace('.', ',');
        }

        function resetForm() {
            document.querySelectorAll('.qty-input').forEach(i => i.value = 0);
            calculateTotal();
            document.getElementById('received_amount').value = '';
            document.getElementById('change-display').innerText = '0,00';
        }
    </script>
</x-app-layout>