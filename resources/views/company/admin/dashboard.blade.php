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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Card de Cadastro/Edição de Eventos -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-8 border border-gray-200">
                <h3 id="form-title" class="font-bold text-lg mb-6 text-gray-800">Criar Novo Evento</h3>
                <form id="event-form" action="{{ route('company.events.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="method-field" name="_method" value="POST">

                    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.5rem;"
                        class="mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Evento</label>
                            <input type="text" id="name" name="name" class="w-full border-gray-300 rounded shadow-sm"
                                placeholder="Ex: Festa de Fim de Ano" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
                            <input type="datetime-local" id="start_date" name="start_date"
                                class="w-full border-gray-300 rounded shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data Término</label>
                            <input type="datetime-local" id="end_date" name="end_date"
                                class="w-full border-gray-300 rounded shadow-sm" required>
                        </div>
                    </div>
                    <div class="flex justify-center gap-4">
                        <button type="submit" id="btn-submit"
                            class="bg-blue-600 text-white px-8 py-2 rounded font-bold hover:bg-blue-700 transition">Salvar
                            Evento</button>
                        <button type="button" onclick="resetForm()" id="btn-cancel"
                            class="hidden bg-gray-400 text-white px-8 py-2 rounded font-bold hover:bg-gray-500 transition">Cancelar</button>
                    </div>
                </form>
            </div>

            <!-- Grid de Cards de Eventos -->
            <div class="flex flex-wrap gap-4">
                @forelse($events as $event)
                <div
                    class="w-56 bg-white border border-gray-200 rounded shadow-sm p-4 flex flex-col items-center text-center">
                    <h4 class="font-bold text-gray-800 mb-1 truncate w-full">{{ $event->name }}</h4>
                    <p class="text-[14px] text-green-700 uppercase">Início: {{
                        \Carbon\Carbon::parse($event->start_date)->format('d/m H:i') }}</p>
                    <p class="text-[14px] text-orange-400 uppercase mb-4">Fim: {{
                        \Carbon\Carbon::parse($event->end_date)->format('d/m H:i') }}</p>

                    <div class="flex flex-col w-full gap-2">
                        @if($event->status !== 'iniciado' && $event->status !== 'encerrado')
                        <form action="{{ route('company.events.start', $event->id) }}" method="POST">
                            @csrf
                            <button
                                class="w-full bg-green-500 text-white py-1.5 rounded text-xs font-bold uppercase hover:bg-green-600">Iniciar</button>
                        </form>
                        @elseif($event->status === 'iniciado')
                        <form action="{{ route('company.events.stop', $event->id) }}" method="POST">
                            @csrf
                            <button
                                class="w-full bg-red-500 text-white py-1.5 rounded text-xs font-bold uppercase hover:bg-red-600">Encerrar</button>
                        </form>
                        @endif

                        <div class="grid grid-cols-2 gap-2">
                            @if($event->status !== 'encerrado')
                            <button type="button"
                                onclick="editEvent('{{ $event->id }}', '{{ $event->name }}', '{{ $event->start_date }}', '{{ $event->end_date }}')"
                                class="bg-blue-500 text-white py-1.5 rounded text-xs hover:bg-blue-600">Editar</button>
                            @else
                            <button disabled
                                class="bg-gray-300 text-gray-500 py-1.5 rounded text-xs cursor-not-allowed">Editar</button>
                            @endif
                            <a href="{{ route('company.events.employees', $event->id) }}"
                                class="bg-yellow-500 text-white py-1.5 rounded text-xs hover:bg-yellow-600">Func.</a>
                        </div>

                        <!-- Botões adicionais -->
                        <a href="{{ route('company.events.products.index', ['event_id' => $event->id]) }}"
                            class="w-full bg-gray-500 text-white py-1.5 rounded text-xs hover:bg-gray-600">Produtos</a>

                        <!-- Link do Relatório movido para dentro do Loop -->
                        <a href="{{ route('company.events.report', $event->id) }}"
                            class="w-full bg-indigo-600 text-white py-1.5 rounded text-xs hover:bg-indigo-700">Relatório</a>

                        @if(is_null($event->started_at))
                        <form action="{{ route('company.events.destroy', $event->id) }}" method="POST"
                            onsubmit="return confirm('Tem certeza que deseja excluir?')">
                            @csrf
                            @method('DELETE')
                            <button
                                class="w-full mt-1 bg-red-600 text-white py-1 rounded text-[10px] uppercase hover:bg-red-700">Excluir
                                Evento</button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-gray-500">Nenhum evento cadastrado.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        function editEvent(id, name, start, end) {
            document.getElementById('form-title').innerText = 'Editando: ' + name;
            document.getElementById('event-form').action = '/events/' + id;
            document.getElementById('method-field').value = 'PUT';
            document.getElementById('name').value = name;
            document.getElementById('start_date').value = start.slice(0, 16);
            document.getElementById('end_date').value = end.slice(0, 16);
            document.getElementById('btn-submit').innerText = 'Atualizar Evento';
            document.getElementById('btn-cancel').classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function resetForm() {
            document.getElementById('form-title').innerText = 'Criar Novo Evento';
            document.getElementById('event-form').action = '{{ route("company.events.store") }}';
            document.getElementById('method-field').value = 'POST';
            document.getElementById('event-form').reset();
            document.getElementById('btn-submit').innerText = 'Salvar Evento';
            document.getElementById('btn-cancel').classList.add('hidden');
        }
    </script>
</x-app-layout>