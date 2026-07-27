<x-app-layout>
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Gestión de cobros</h1>
            <p class="text-sm text-slate-500 mt-0.5">Cuentas por cobrar importadas con saldo pendiente, con su bitácora de gestión.</p>
        </div>

        <a href="{{ route('receivables.import') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 text-white font-semibold text-sm hover:bg-brand-700 shadow-sm shadow-brand-900/10 transition">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l-4-4m4 4l4-4" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2.5A1.5 1.5 0 005.5 20h13a1.5 1.5 0 001.5-1.5V16" />
            </svg>
            Importar cartera
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h4.5l1.5 3h6l1.5-3H21" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.5 6h13l2 6.5V18a1.5 1.5 0 01-1.5 1.5h-14A1.5 1.5 0 013.5 18v-5.5L5.5 6z" />
                </svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Cuentas abiertas</div>
                <div class="text-2xl font-bold text-slate-900">{{ number_format($summary['total_open']) }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3" />
                    <circle cx="12" cy="12" r="8.5" stroke-linecap="round" />
                </svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide">A gestionar hoy</div>
                <div class="text-2xl font-bold text-amber-600">{{ number_format($summary['due_today']) }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12M8 9.5c0-1.4 1.6-2.5 4-2.5s4 1.1 4 2.5-1.6 2.5-4 2.5-4 1.1-4 2.5 1.6 2.5 4 2.5 4-1.1 4-2.5" />
                </svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Saldo pendiente</div>
                <div class="text-2xl font-bold text-slate-900">${{ number_format($summary['total_balance'], 2) }}</div>
            </div>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Cliente, documento...">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Estado</label>
                <select name="status_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="">Todos</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}" @selected(request('status_id') == $status->id)>{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="w-full rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold hover:bg-slate-800">Filtrar</button>
                <a href="{{ route('collections.index') }}" class="rounded-lg bg-white border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Limpiar</a>
            </div>
        </div>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Documento</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Cliente</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Estado</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Saldo</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Mora</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Próxima gestión</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($receivables as $row)
                        @php($receivable = $row['receivable'])
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-4 text-sm text-slate-700 font-medium">{{ $receivable->document_number ?: 'Sin documento' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $receivable->customer_name }}</td>
                            <td class="px-5 py-4 text-sm">
                                @if($row['tracking']?->status)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-white" style="background-color: {{ $row['tracking']->status->color }}">
                                        {{ $row['tracking']->status->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-slate-100 text-slate-500">Sin gestión</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-800 font-semibold text-right">${{ number_format((float) $receivable->balance, 2) }}</td>
                            <td class="px-5 py-4 text-sm text-right">
                                @if($row['days_overdue'] > 0)
                                    <span class="text-red-600 font-semibold">{{ $row['days_overdue'] }}d</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm">
                                @if($row['tracking']?->next_follow_up_at)
                                    <span class="{{ $row['tracking']->next_follow_up_at->isPast() ? 'text-red-600 font-semibold' : 'text-slate-700' }}">
                                        {{ $row['tracking']->next_follow_up_at->format('d/m/Y H:i') }}
                                    </span>
                                @else
                                    <span class="text-slate-400">Sin gestión</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if($row['tracking'])
                                    <a href="{{ route('collections.show', $row['tracking']) }}" class="text-brand-600 hover:text-brand-800 font-semibold text-sm">Ver</a>
                                @else
                                    <form method="POST" action="{{ route('collections.from-receivable', $receivable) }}">
                                        @csrf
                                        <button class="text-brand-600 hover:text-brand-800 font-semibold text-sm">Gestionar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center">
                                <div class="mx-auto w-12 h-12 rounded-2xl bg-brand-50 text-brand-500 flex items-center justify-center mb-3">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h4.5l1.5 3h6l1.5-3H21" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.5 6h13l2 6.5V18a1.5 1.5 0 01-1.5 1.5h-14A1.5 1.5 0 013.5 18v-5.5L5.5 6z" />
                                    </svg>
                                </div>
                                <p class="text-slate-600 font-medium">Todavía no tienes cuentas por cobrar</p>
                                <p class="text-sm text-slate-400 mt-1">
                                    <a href="{{ route('receivables.import') }}" class="text-brand-600 font-semibold hover:underline">Importa tu primer CSV</a>
                                    para empezar a gestionar tu cartera.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>
