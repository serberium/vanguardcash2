<?php

namespace App\Http\Controllers\Company\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index() {
        $events = Event::where('company_id', auth()->user()->company_id)->get();
        return view('company.admin.events.index', compact('events'));
    }

    public function start(Request $request, $id) {
        $event = Event::findOrFail($id);

        if (!auth()->user()->canManageEvent($event)) {
            abort(403, 'Você não tem permissão para esta ação.');
        }
        
        if (!$event->canStart()) {
            return back()->with('error', 'Requisitos não atendidos ou fora do período.');
        }

        $event->update(['status' => 'iniciado']);
        return back()->with('success', 'Evento iniciado!');
    }

    public function stop(Request $request, $id) {
        $event = Event::findOrFail($id);
        
        if (!auth()->user()->canManageEvent($event)) {
            abort(403, 'Acesso negado.');
        }

        $event->update(['status' => 'encerrado']);
        return back()->with('success', 'Evento encerrado!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        Event::create([
            'name'       => $request->name,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'company_id' => auth()->user()->company_id,
            'status'     => 'criado',
        ]);

        return back()->with('success', 'Evento criado com sucesso!');
    }

    public function destroy($id)
    {
        $event = Event::where('company_id', auth()->user()->company_id)->findOrFail($id);

        if (!is_null($event->started_at)) {
            return back()->with('error', 'Este evento não pode ser excluído pois já foi iniciado.');
        }

        $event->delete();
        return back()->with('success', 'Evento excluído com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $event = Event::where('company_id', auth()->user()->company_id)->findOrFail($id);

        if ($event->status === 'encerrado') {
            return back()->with('error', 'Eventos encerrados não podem ser editados.');
        }

        $event->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return back()->with('success', 'Evento atualizado com sucesso!');
    }

    public function manageEmployees($id)
    {
        $event = Event::with('employees')
                      ->where('company_id', auth()->user()->company_id)
                      ->findOrFail($id);

        $allEmployees = Employee::where('company_id', auth()->user()->company_id)->get();
        
        $busyEmployeeIds = DB::table('event_employee')
            ->join('events', 'event_employee.event_id', '=', 'events.id')
            ->where('events.status', '!=', 'encerrado')
            ->pluck('event_employee.employee_id')
            ->toArray();

        $availableEmployees = $allEmployees->whereNotIn('id', $busyEmployeeIds);

        return view('company.admin.events.employees', compact('event', 'availableEmployees'));
    }

    public function addEmployee(Request $request, $id)
    {
        $request->validate(['employee_id' => 'required', 'role_in_event' => 'required']);
        
        $event = Event::findOrFail($id);
        $employee = Employee::findOrFail($request->employee_id);

        DB::transaction(function () use ($event, $employee, $request) {
            $event->employees()->syncWithoutDetaching([
                $employee->id => ['role_in_event' => $request->role_in_event]
            ]);

            $event->users()->syncWithoutDetaching([
                $employee->user_id => [
                    'role' => $request->role_in_event,
                    'is_active' => true
                ]
            ]);

            $employee->update(['role' => $request->role_in_event]);
        });

        return back()->with('success', 'Funcionário associado com sucesso!');
    }

    public function updateEmployeeRole(Request $request, $id)
    {
        $request->validate(['employee_id' => 'required', 'role_in_event' => 'required']);
        
        $event = Event::findOrFail($id);
        $employee = Employee::findOrFail($request->employee_id);

        DB::transaction(function () use ($event, $employee, $request) {
            $event->employees()->updateExistingPivot($employee->id, [
                'role_in_event' => $request->role_in_event
            ]);

            $event->users()->updateExistingPivot($employee->user_id, [
                'role' => $request->role_in_event
            ]);

            $employee->update(['role' => $request->role_in_event]);
        });

        return back()->with('success', 'Cargo atualizado com sucesso!');
    }

    public function removeEmployee($id, $employeeId)
    {
        $event = Event::where('company_id', auth()->user()->company_id)->findOrFail($id);
        $employee = Employee::findOrFail($employeeId);

        DB::transaction(function () use ($event, $employee) {
            $event->employees()->detach($employee->id);
            $event->users()->detach($employee->user_id);
        });

        return back()->with('success', 'Funcionário removido do evento!');
    }

    public function report(Request $request, $id)
    {
        $event = \App\Models\Event::findOrFail($id);
        
        // Datas para o filtro de período
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // 1. Relatório do Evento (Total acumulado)
        // Pegamos apenas as vendas (total_amount > 0) ligadas aos caixas deste evento
        $query = \App\Models\Sale::whereHas('cashRegister', function($q) use ($id) {
            $q->where('event_id', $id);
        })->where('total_amount', '>', 0); // Filtra apenas vendas positivas

        $eventSales = $query->get();
        

        // 2. Relatório Diário (Considera o evento atual e hoje)
        $dailySales = $eventSales->where('created_at', '>=', now()->startOfDay());

        // 3. Relatório por Período
        $periodSales = $eventSales->whereBetween('created_at', [
            $startDate . ' 00:00:00', 
            $endDate . ' 23:59:59'
        ]);

        return view('company.admin.report', compact('event', 'eventSales', 'dailySales', 'periodSales', 'startDate', 'endDate'));
    }
}