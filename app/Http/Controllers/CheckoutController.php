<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function status()
    {
        $activeEvent = Auth::user()->events()->where('status', 'iniciado')->first();
        if (!$activeEvent) return redirect()->route('dashboard');

        $caixaAberto = CashRegister::where('event_id', $activeEvent->id)
                                   ->where('user_id', Auth::id())
                                   ->where('status', 'aberto')
                                   ->latest()
                                   ->first();
        return view('company.checkout.cashregister', compact('caixaAberto', 'activeEvent'));
    }

    public function open(Request $request)
    {
        $request->validate(['opening_amount' => 'required|numeric']);
        CashRegister::create([
            'event_id' => $request->event_id,
            'user_id' => Auth::id(),
            'opening_amount' => $request->opening_amount,
            'status' => 'aberto',
            'opened_at' => now()
        ]);
        return redirect()->route('dashboard')->with('success', 'Caixa aberto!');
    }

    public function close(Request $request)
    {
        $request->validate(['closing_amount' => 'required|numeric']);
        $caixa = CashRegister::where('user_id', Auth::id())->where('status', 'aberto')->first();
        
        if (!$caixa) return redirect()->route('dashboard')->with('error', 'Nenhum caixa aberto encontrado.');
        
        $caixa->update([
            'closing_amount' => $request->closing_amount,
            'status' => 'fechado',
            'closed_at' => now()
        ]);
        return redirect()->route('dashboard')->with('success', 'Caixa fechado!');
    }

    public function withdrawView()
    {
        $caixaAberto = CashRegister::where('user_id', Auth::id())->where('status', 'aberto')->first();
        if (!$caixaAberto) return redirect()->route('dashboard')->with('error', 'Abra o caixa primeiro.');
        return view('company.checkout.cashdrop', compact('caixaAberto'));
    }

    public function withdraw(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:0.01']);
        $caixa = CashRegister::where('user_id', Auth::id())->where('status', 'aberto')->first();
        
        if (!$caixa) return redirect()->route('dashboard')->with('error', 'Nenhum caixa aberto encontrado.');

        Sale::create([
            'cash_register_id' => $caixa->id,
            'total_amount' => -abs($request->amount),
            'received_amount' => 0,
            'change_amount' => 0,
            'payment_method' => 'dinheiro',
            'is_internal_consumption' => false
        ]);
        return redirect()->route('checkout.withdraw')->with('success', 'Sangria realizada.');
    }

    public function saleView()
    {
        $activeEvent = Auth::user()->events()->where('status', 'iniciado')->first();
        $caixaAberto = CashRegister::where('event_id', $activeEvent->id)
                                   ->where('user_id', Auth::id())
                                   ->where('status', 'aberto')
                                   ->latest()
                                   ->first();

        if (!$caixaAberto) return redirect()->route('dashboard')->with('error', 'Abra o caixa.');
        $products = $activeEvent->products()->where('stock', '>', 0)->get();
        return view('company.checkout.sale', compact('caixaAberto', 'products'));
    }

    public function storeSale(Request $request)
    {
        $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'items' => 'required|array',
            'payment_method' => 'required',
        ]);

        $saleId = null;

        DB::transaction(function () use ($request, &$saleId) {
            $totalAmount = 0;
            $items = array_filter($request->items, fn($q) => $q > 0);

            foreach ($items as $productId => $quantity) {
                $product = Product::findOrFail($productId);
                $fatorBaixa = $product->package_size ?? 1;
                $totalBaixa = $quantity * $fatorBaixa;
                
                if (!$product->sell($totalBaixa)) {
                    throw new \Exception("Estoque insuficiente para {$product->name}");
                }
                $totalAmount += ($product->unit_price * $quantity);
            }

            $isInternal = ($request->payment_method === 'consumo_interno');
            $received = in_array($request->payment_method, ['pix', 'cartao', 'consumo_interno']) 
                        ? $totalAmount 
                        : ($request->received_amount ?? $totalAmount);

            $sale = Sale::create([
                'cash_register_id' => $request->cash_register_id,
                'total_amount' => $isInternal ? 0 : $totalAmount,
                'received_amount' => $isInternal ? 0 : $received,
                'change_amount' => $isInternal ? 0 : ($received - $totalAmount),
                'payment_method' => $request->payment_method,
                'is_internal_consumption' => $isInternal ? true : false
            ]);

            foreach ($items as $productId => $quantity) {
                $product = Product::findOrFail($productId);
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $product->unit_price,
                    'total_price' => ($product->unit_price * $quantity)
                ]);
            }
            
            $saleId = $sale->id;
        });

        return redirect()->route('checkout.print', $saleId)->with('success', 'Venda realizada com sucesso!');
    }

    public function print($id)
    {
        $sale = Sale::with('items.product')->findOrFail($id);
        return view('company.checkout.print', compact('sale'));
    }
}