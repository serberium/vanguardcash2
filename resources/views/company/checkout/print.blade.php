<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cupom de Venda - {{ $sale->id }}</title>
    <style>
        body {
            width: 72mm;
            margin: 0;
            padding: 5px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            color: #000;
        }

        .text-center {
            text-align: center;
        }

        .border-top-bottom {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
            margin: 5px 0;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .total-row {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            @page {
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <script>
        window.onload = function() {
            window.print();
            
            // Redireciona de volta para a tela de vendas após 1 segundo
            setTimeout(function() {
                window.location.href = "{{ route('checkout.sale') }}";
            }, 1000);
        };
    </script>

    <div class="text-center">
        <h3>COMPROVANTE DE VENDA</h3>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="border-top-bottom">
        @foreach($sale->items as $item)
        <div class="item-row">
            <span>{{ $item->quantity }}x {{ $item->product->name }}</span>
            <span>R$ {{ number_format($item->total_price, 2, ',', '.') }}</span>
        </div>
        @endforeach
    </div>

    <div class="total-row">
        <div class="item-row">
            <span>TOTAL:</span>
            <span>R$ {{ number_format($sale->total_amount, 2, ',', '.') }}</span>
        </div>
    </div>

    <div class="text-center" style="margin-top: 20px;">
        <p>Forma de Pagamento: {{ ucfirst($sale->payment_method) }}</p>
        <p>-------------------------</p>
        <p>Obrigado pela preferência!</p>
    </div>

</body>

</html>