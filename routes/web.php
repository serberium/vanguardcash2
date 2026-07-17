<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Company\Admin\EventController;
use App\Http\Controllers\Company\Admin\EmployeeController;
use App\Http\Controllers\Company\Admin\ProductController;
use App\Http\Controllers\CheckoutController;

Route::get('/', function () { return view('welcome'); });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'is_sadmin'])->prefix('admin')->group(function () {
    Route::get('/companies', [CompanyController::class, 'index'])->name('admin.companies.index');
    Route::post('/companies', [CompanyController::class, 'store'])->name('admin.companies.store');
    Route::put('/companies/{company}/renew', [CompanyController::class, 'renew'])->name('admin.companies.renew');
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('admin.companies.destroy');
});

Route::middleware(['auth', 'check.active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rotas de Eventos
    Route::post('/events/{id}/start', [EventController::class, 'start'])->name('company.events.start');
    Route::post('/events/{id}/stop', [EventController::class, 'stop'])->name('company.events.stop');
    Route::post('/events', [EventController::class, 'store'])->name('company.events.store');
    Route::put('/events/{id}', [EventController::class, 'update'])->name('company.events.update');
    Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('company.events.destroy');
    Route::get('/eventos/criar', [EventController::class, 'create'])->name('company.events.create');
    Route::get('/events/{id}/employees', [EventController::class, 'manageEmployees'])->name('company.events.employees');
    
    // Rotas de Associação
    Route::post('/events/{id}/add-employee', [EventController::class, 'addEmployee'])->name('company.events.add-employee');
    Route::post('/events/{id}/update-employee-role', [EventController::class, 'updateEmployeeRole'])->name('company.events.update-employee-role');
    Route::delete('/events/{id}/remove-employee/{employeeId}', [EventController::class, 'removeEmployee'])->name('company.events.remove-employee');
    
    // Rotas de Funcionários
    Route::get('/funcionarios', [EmployeeController::class, 'index'])->name('company.employees');
    Route::post('/funcionarios', [EmployeeController::class, 'store'])->name('company.employees.store');
    Route::put('/funcionarios/{id}', [EmployeeController::class, 'update'])->name('company.employees.update');
    Route::delete('/funcionarios/{id}', [EmployeeController::class, 'destroy'])->name('company.employees.destroy');

    // Rotas de Produtos do Evento
    Route::get('/events/{event_id}/products', [ProductController::class, 'index'])->name('company.events.products.index');
    Route::post('/events/{event_id}/products', [ProductController::class, 'store'])->name('company.events.products.store');
    
    Route::put('/events/{event_id}/products/{id}', [ProductController::class, 'update'])->name('company.events.products.update');
    Route::delete('/events/{event_id}/products/{id}', [ProductController::class, 'destroy'])->name('company.events.products.destroy');
    
    // Rotas de Estoque
    Route::post('/events/products/{id}/add-production', [ProductController::class, 'addProduction'])->name('company.events.products.add-production');
    Route::post('/events/products/{id}/finalize-production', [ProductController::class, 'finalizeProduction'])->name('company.events.products.finalize-production');

    // ROTAS DO CAIXA
    Route::get('/checkout/status', [CheckoutController::class, 'status'])->name('checkout.status');
    Route::post('/checkout/open', [CheckoutController::class, 'open'])->name('checkout.open');
    Route::post('/checkout/close', [CheckoutController::class, 'close'])->name('checkout.close');
    
    // Rotas de Sangria
    Route::get('/checkout/withdraw', [CheckoutController::class, 'withdrawView'])->name('checkout.withdraw');
    Route::post('/checkout/withdraw', [CheckoutController::class, 'withdraw'])->name('checkout.withdraw.store');
    
    // Rotas de Venda e Impressão
    Route::get('/checkout/sale', [CheckoutController::class, 'saleView'])->name('checkout.sale');
    Route::post('/checkout/sale', [CheckoutController::class, 'storeSale'])->name('checkout.sale.store');
    Route::get('/checkout/print/{sale}', [CheckoutController::class, 'print'])->name('checkout.print');

    //Rotas Relatórios
    Route::get('/events/{id}/report', [App\Http\Controllers\Company\Admin\EventController::class, 'report'])->name('company.events.report');
});

require __DIR__.'/auth.php';