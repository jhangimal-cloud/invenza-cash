<x-app-layout>
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Cash Flow</h1>
            <p class="text-sm text-slate-500 mt-0.5">Ingresos esperados por cobros menos egresos esperados por pagar, mes a mes.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('payables.import') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white font-semibold text-sm hover:bg-slate-800 transition">
                Importar cuentas por pagar
            </a>
            <a href="{{ route('collections.forecast') }}" class="rounded-xl bg-white border border-slate-300 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 font-medium">
                Ver detalle de ingresos
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex gap-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs px-4 py-3">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-5 h-5 shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86l-8.18 14.18A1.5 1.5 0 003.34 20.5h17.32a1.5 1.5 0 001.23-2.46L13.71 3.86a1.5 1.5 0 00-2.42 0z" />
        </svg>
        <div>
            <strong>Alcance actual:</strong> los egresos incluyen solo las cuentas por pagar importadas — este Cash Flow
            todavía no considera nómina ni gastos operativos generales (alquiler, servicios, etc.) que no se hayan
            cargado como cuenta por pagar.
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Concepto</th>
                        @foreach($monthKeys as $key)
                            <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ $monthLabels[$key] }}</th>
                        @endforeach
                        <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Más adelante</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="px-5 py-4 text-sm font-semibold text-emerald-700">Ingresos esperados</td>
                        @foreach($monthKeys as $key)
                            <td class="px-5 py-4 text-sm text-right text-emerald-700 font-medium">${{ number_format($incomeBuckets[$key]['total'], 2) }}</td>
                        @endforeach
                        <td class="px-5 py-4 text-sm text-right text-emerald-700 font-medium">${{ number_format($incomeBuckets['later']['total'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-4 text-sm font-semibold text-red-700">Egresos esperados</td>
                        @foreach($monthKeys as $key)
                            <td class="px-5 py-4 text-sm text-right text-red-700 font-medium">${{ number_format($expenseBuckets[$key]['total'], 2) }}</td>
                        @endforeach
                        <td class="px-5 py-4 text-sm text-right text-red-700 font-medium">${{ number_format($expenseBuckets['later']['total'], 2) }}</td>
                    </tr>
                    <tr class="bg-slate-50">
                        <td class="px-5 py-4 text-sm font-bold text-slate-900">Flujo neto</td>
                        @foreach($monthKeys as $key)
                            <td class="px-5 py-4 text-sm text-right font-bold {{ $netByMonth[$key] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                                ${{ number_format($netByMonth[$key], 2) }}
                            </td>
                        @endforeach
                        <td class="px-5 py-4 text-sm text-right font-bold {{ $netByMonth['later'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                            ${{ number_format($netByMonth['later'], 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border-2 border-red-200 p-5">
            <div class="text-xs font-semibold text-red-600 uppercase tracking-wide">Ingresos vencidos sin gestión</div>
            <div class="text-xl font-bold text-red-700 mt-1">${{ number_format($incomeBuckets['overdue_no_promise']['total'], 2) }}</div>
            <div class="text-xs text-slate-500 mt-0.5">{{ $incomeBuckets['overdue_no_promise']['count'] }} cuenta(s) — no se le ha dado seguimiento</div>
        </div>
        <div class="bg-white rounded-2xl border-2 border-red-200 p-5">
            <div class="text-xs font-semibold text-red-600 uppercase tracking-wide">Egresos vencidos</div>
            <div class="text-xl font-bold text-red-700 mt-1">${{ number_format($expenseBuckets['overdue']['total'], 2) }}</div>
            <div class="text-xs text-slate-500 mt-0.5">{{ $expenseBuckets['overdue']['count'] }} cuenta(s) por pagar</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h2 class="font-bold text-slate-900">Detalle de egresos (cuentas por pagar pendientes)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Documento</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Proveedor</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Saldo</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Mes estimado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($expenseRows as $row)
                        @php($payable = $row['payable'])
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-4 text-sm text-slate-700 font-medium">{{ $payable->document_number ?: 'Sin documento' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $payable->vendor_name }}</td>
                            <td class="px-5 py-4 text-sm text-slate-800 font-semibold text-right">${{ number_format((float) $payable->balance, 2) }}</td>
                            <td class="px-5 py-4 text-sm">
                                @if($row['bucket'] === 'overdue')
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-white bg-red-600">Vencido</span>
                                @elseif($row['bucket'] === 'later')
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-white bg-slate-500">Más adelante</span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-white bg-red-500">
                                        {{ $monthLabels[$row['bucket']] ?? $row['bucket'] }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-slate-500">
                                No hay cuentas por pagar todavía.
                                <a href="{{ route('payables.import') }}" class="text-brand-600 font-semibold hover:underline">Importa tu primer CSV</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>
