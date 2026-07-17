<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\CashRegister;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. SAdmin
        if ($user->isSadmin()) {
            $companies = \App\Models\Company::all(); 
            return view('admin.dashboard', compact('companies'));
        }

        // 2. Admin da Empresa
        if ($user->isAdmin()) {
            $events = \App\Models\Event::where('company_id', $user->company_id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            return view('company.admin.dashboard', compact('events'));
        }

        // 3. Funcionário
        $role = $user->activeRole;
        $activeEvent = $user->events()->where('status', 'iniciado')->first();

        if (!$activeEvent) {
            abort(403, 'Você não está escalado em nenhum evento ativo no momento.');
        }

        $products = $activeEvent->products;

        return match(trim(strtolower($role))) {
            'caixa' => $this->handleCheckout($activeEvent, $products),
            'producao' => view('company.production.dashboard', compact('activeEvent', 'products')),
            default => abort(403, 'Papel não autorizado para acesso ao sistema.'),
        };
    }

    private function handleCheckout($activeEvent, $products)
    {
        $caixa = CashRegister::where('event_id', $activeEvent->id)
                             ->where('user_id', Auth::id())
                             ->where('status', 'aberto')
                             ->latest()
                             ->first();

        $caixaAberto = ($caixa && $caixa->status === 'aberto') ? $caixa : null;
        $resumo = [];

        if ($caixa) {
            $vendas = Sale::where('cash_register_id', $caixa->id)->get();
            
            $resumo = [
                'abertura'     => $caixa->opening_amount,
                'sangria'      => $vendas->where('total_amount', '<', 0)->sum('total_amount'),
                'dinheiro'     => $vendas->where('payment_method', 'dinheiro')->where('total_amount', '>', 0)->sum('total_amount'),
                'pix'          => $vendas->where('payment_method', 'pix')->sum('total_amount'),
                'cartao'       => $vendas->where('payment_method', 'cartao')->sum('total_amount'),
                // CORREÇÃO: Agora soma o valor total das vendas de consumo interno
                'interno'      => $vendas->where('is_internal_consumption', true)->sum(function($sale) {
                                      return $sale->items->sum('total_price');
                                  }),
                'total_vendas' => $vendas->where('total_amount', '>=', 0)->sum('total_amount'),
            ];
        }

        return view('company.checkout.dashboard', compact('activeEvent', 'products', 'caixaAberto', 'caixa', 'resumo'));
    }
}