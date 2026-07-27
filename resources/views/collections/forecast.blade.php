<x-app-layout>
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Proyección mensual de cobros</h1>
            <p class="text-sm text-slate-500 mt-0.5">Cuánto esperas cobrar por mes, según promesas de pago registradas y vencimientos.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('collections.cashflow') }}" class="rounded-xl bg-slate-900 text-white px-4 py-2.5 text-sm font-semibold hover:bg-slate-800">
                Ver Cash Flow
            </a>
            <a href="{{ route('collections.index') }}" class="rounded-xl bg-white border border-slate-300 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 font-medium">
                ← Volver a la bandeja
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl border-2 border-red-200 p-5">
            <div class="text-xs font-semibold text-red-600 uppercase tracking-wide">Vencido sin gestión</div>
            <div class="text-xl font-bold text-red-700 mt-1">${{ number_format($buckets['overdue_no_promise']['total'], 2) }}</div>
            <div class="text-xs text-slate-500 mt-0.5">{{ $buckets['overdue_no_promise']['count'] }} cuenta(s)</div>
        </div>

        @foreach($monthKeys as $key)
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ $monthLabels[$key] }}</div>
                <div class="text-xl font-bold text-slate-900 mt-1">${{ number_format($buckets[$key]['total'], 2) }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $buckets[$key]['count'] }} cuenta(s)</div>
            </div>
        @endforeach

        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Más adelante</div>
            <div class="text-xl font-bold text-slate-900 mt-1">${{ number_format($buckets['later']['total'], 2) }}</div>
            <div class="text-xs text-slate-500 mt-0.5">{{ $buckets['later']['count'] }} cuenta(s)</div>
        </div>
    </div>

    <div class="flex gap-3 rounded-xl bg-brand-50 border border-brand-100 text-brand-800 text-xs px-4 py-3">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-5 h-5 shrink-0">
            <circle cx="12" cy="12" r="9" />
            <path stroke-linecap="round" d="M12 8h.01M11 12h1v4h1" />
        </svg>
        <div>
            El monto de cada cuenta se asigna primero por su promesa de pago más reciente; si no tiene promesa, se usa
            el mes de vencimiento (solo si es futuro). Las cuentas ya vencidas sin ninguna promesa quedan en
            "Vencido sin gestión" para no inflar la proyección con dinero incierto.
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Documento</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Cliente</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Saldo</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Mes estimado</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Estado gestión</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rows as $row)
                        @php($receivable = $row['receivable'])
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-4 text-sm text-slate-700 font-medium">{{ $receivable->document_number ?: 'Sin documento' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $receivable->customer_name }}</td>
                            <td class="px-5 py-4 text-sm text-slate-800 font-semibold text-right">${{ number_format((float) $receivable->balance, 2) }}</td>
                            <td class="px-5 py-4 text-sm">
                                @if($row['bucket'] === 'overdue_no_promise')
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-white bg-red-600">Vencido sin gestión</span>
                                @elseif($row['bucket'] === 'later')
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-white bg-slate-500">Más adelante</span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-white bg-brand-600">
                                        {{ $monthLabels[$row['bucket']] ?? $row['bucket'] }}
                                    </span>
                                @endif
                                @if($row['promised_payment_date'])
                                    <div class="text-xs text-violet-600 mt-1">Promesa: {{ $row['promised_payment_date']->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm">
                                @if($row['tracking']?->status)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-white" style="background-color: {{ $row['tracking']->status->color }}">
                                        {{ $row['tracking']->status->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-slate-100 text-slate-500">Sin gestión</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if($row['tracking'])
                                    <a href="{{ route('collections.show', $row['tracking']) }}" class="text-brand-600 hover:text-brand-800 font-semibold text-sm">Ver</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-500">No hay cuentas pendientes de cobro.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>
