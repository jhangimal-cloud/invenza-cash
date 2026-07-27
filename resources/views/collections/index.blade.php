<x-app-layout>
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Gestión de cobros</h1>
                <p class="text-sm text-slate-500">Cuentas por cobrar importadas con saldo pendiente, con su bitácora de gestión.</p>
            </div>

            <a href="{{ route('receivables.import') }}"
               class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700">
                Importar cartera
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <div class="text-sm text-slate-500">Cuentas abiertas</div>
                <div class="text-2xl font-bold text-slate-800">{{ number_format($summary['total_open']) }}</div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <div class="text-sm text-slate-500">A gestionar hoy</div>
                <div class="text-2xl font-bold text-amber-600">{{ number_format($summary['due_today']) }}</div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <div class="text-sm text-slate-500">Saldo total pendiente</div>
                <div class="text-2xl font-bold text-slate-800">${{ number_format($summary['total_balance'], 2) }}</div>
            </div>
        </div>

        <form method="GET" class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="w-full rounded-lg border-slate-300" placeholder="Cliente, documento...">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Estado</label>
                    <select name="status_id" class="w-full rounded-lg border-slate-300">
                        <option value="">Todos</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" @selected(request('status_id') == $status->id)>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button class="w-full rounded-lg bg-slate-800 text-white px-4 py-2 hover:bg-slate-900">Filtrar</button>
                    <a href="{{ route('collections.index') }}" class="rounded-lg bg-white border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Limpiar</a>
                </div>
            </div>
        </form>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Documento</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Cliente</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Estado</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase">Saldo</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase">Días de mora</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Próxima gestión</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($receivables as $row)
                            @php($receivable = $row['receivable'])
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $receivable->document_number ?: 'Sin documento' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $receivable->customer_name }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($row['tracking']?->status)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-white" style="background-color: {{ $row['tracking']->status->color }}">
                                            {{ $row['tracking']->status->name }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">Sin gestión</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700 text-right">${{ number_format((float) $receivable->balance, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right">
                                    @if($row['days_overdue'] > 0)
                                        <span class="text-red-600 font-semibold">{{ $row['days_overdue'] }}</span>
                                    @else
                                        <span class="text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($row['tracking']?->next_follow_up_at)
                                        <span class="{{ $row['tracking']->next_follow_up_at->isPast() ? 'text-red-600 font-semibold' : 'text-slate-700' }}">
                                            {{ $row['tracking']->next_follow_up_at->format('d/m/Y H:i') }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">Sin gestión</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($row['tracking'])
                                        <a href="{{ route('collections.show', $row['tracking']) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">Ver</a>
                                    @else
                                        <form method="POST" action="{{ route('collections.from-receivable', $receivable) }}">
                                            @csrf
                                            <button class="text-blue-600 hover:text-blue-800 font-semibold text-sm">Gestionar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                    No hay cuentas por cobrar todavía. <a href="{{ route('receivables.import') }}" class="text-indigo-600 underline">Importa tu primer CSV</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
