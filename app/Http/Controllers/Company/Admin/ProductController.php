<?php

namespace App\Http\Controllers\Company\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index($event_id)
    {
        $event = Event::where('company_id', Auth::user()->company_id)->findOrFail($event_id);
        
        // Garante que a relação seja carregada ou retorne uma coleção vazia
        $products = $event->products ?? collect();
        
        return view('company.admin.events.products', compact('products', 'event_id'));
    }

    public function store(Request $request, $event_id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'package_size' => 'required|integer|min:1',
            'low_stock_threshold' => 'required|integer|min:0',
            'critical_stock_threshold' => 'required|integer|min:0',
        ]);

        $event = Event::where('company_id', Auth::user()->company_id)->findOrFail($event_id);

        $product = Product::create([
            'name' => $request->name,
            'unit_price' => $request->unit_price,
            'package_size' => $request->package_size,
            'low_stock_threshold' => $request->low_stock_threshold,
            'critical_stock_threshold' => $request->critical_stock_threshold,
            'company_id' => Auth::user()->company_id,
            'production_stock' => 0,
            'sale_stock' => 0,
        ]);

        $event->products()->attach($product->id, ['price_in_event' => $request->unit_price]);

        return back()->with('success', 'Produto criado e associado ao evento!');
    }

    public function update(Request $request, $id, $event_id)
    {
        $product = Product::where('company_id', Auth::user()->company_id)->findOrFail($id);

        if ($product->sale_stock > 0) {
            return back()->with('error', 'Não é possível alterar produtos com estoque de venda.');
        }

        $request->validate([
            'unit_price' => 'required|numeric|min:0',
            'package_size' => 'required|integer|min:1',
        ]);

        $product->update([
            'unit_price' => $request->unit_price,
            'package_size' => $request->package_size
        ]);

        $product->events()->updateExistingPivot($event_id, ['price_in_event' => $request->unit_price]);

        return back()->with('success', 'Informações atualizadas!');
    }

    public function destroy($id, $event_id)
    {
        $product = Product::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $product->events()->detach($event_id);
        $product->delete();
        return back()->with('success', 'Produto excluído do evento!');
    }

    public function addProduction(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $product = Product::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $product->increment('production_stock', $request->quantity);
        return back()->with('success', 'Adicionado à produção!');
    }

    public function finalizeProduction(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $product = Product::where('company_id', Auth::user()->company_id)->findOrFail($id);
        
        if ($product->finalizeProduction($request->quantity)) {
            return back()->with('success', 'Transferido para venda!');
        }
        return back()->with('error', 'Quantidade insuficiente na produção!');
    }
}