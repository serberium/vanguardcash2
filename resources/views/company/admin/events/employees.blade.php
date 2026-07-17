<x-app-layout>
    <x-slot name="header">
        <!-- Sistema de Abas -->
        <div class="mb-8 border-b border-gray-200">
            <nav class="flex space-x-8">
                <a href="{{ route('dashboard') }}"
                    class="py-4 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600">Gerenciar Eventos</a>
                <a href="{{ route('company.employees') }}"
                    class="py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700">Gerenciar
                    Func.</a>
            </nav>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 ">
            <h2 class="text-xl font-bold mb-6">Funcionários do Evento: {{ $event->name }}</h2>

            <!-- Formulário para Associar -->
            <div class="bg-white p-6 rounded shadow mb-6">
                <form action="{{ route('company.events.add-employee', $event->id) }}" method="POST" class="flex gap-2">
                    @csrf
                    <select name="employee_id" class="border rounded p-2" required>
                        <option value="">Selecione um disponível</option>
                        @foreach($availableEmployees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>

                    <select name="role_in_event" class="border rounded p-2" required>
                        <option value="">Selecione o Cargo</option>
                        @foreach(\App\Enums\EmployeeRole::cases() as $role)
                        <option value="{{ $role->value }}">{{ $role->value }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Adicionar</button>
                </form>
            </div>

            <!-- Lista de Funcionários Associados -->
            @foreach($event->employees as $emp)
            <div class="flex items-center justify-between bg-white p-6 rounded shadow mb-2">
                <span class="font-medium">{{ $emp->name }}</span>

                <div class="flex items-center gap-2">
                    <!-- Formulário de Atualização -->
                    <form action="{{ route('company.events.update-employee-role', $event->id) }}" method="POST"
                        class="flex gap-2">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ $emp->id }}">

                        <select name="role_in_event" class="border rounded p-1 w-32" required>
                            @foreach(\App\Enums\EmployeeRole::cases() as $role)
                            <option value="{{ $role->value }}" {{ $emp->pivot->role_in_event == $role->value ?
                                'selected' : '' }}>
                                {{ $role->value }}
                            </option>
                            @endforeach
                        </select>

                        <button type="submit"
                            class="bg-blue-500 text-white px-2 py-1 rounded text-xs">Atualizar</button>
                    </form>

                    <!-- Formulário de Remoção -->
                    <form action="{{ route('company.events.remove-employee', [$event->id, $emp->id]) }}" method="POST"
                        onsubmit="return confirm('Deseja realmente remover este funcionário do evento?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-red-500 text-white px-2 py-1 pt-2 pb-2 rounded text-xs">Remover</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-app-layout>