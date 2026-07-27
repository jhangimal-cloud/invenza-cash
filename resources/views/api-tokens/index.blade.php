<x-app-layout>
<div class="max-w-4xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Conexión API</h1>
        <p class="text-sm text-slate-500 mt-0.5">
            Genera un token para que un sistema externo (SAP, CONTPAQi, u otro) empuje tu cartera directamente, sin CSV.
        </p>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    @if(session('plainTextToken'))
        <div class="rounded-xl bg-amber-50 border-2 border-amber-300 p-5">
            <div class="text-sm font-bold text-amber-900 mb-2">⚠ Copia este token ahora — no se volverá a mostrar</div>
            <code class="block bg-white border border-amber-200 rounded-lg px-4 py-3 text-sm font-mono text-slate-800 break-all select-all">{{ session('plainTextToken') }}</code>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h2 class="font-bold text-slate-900 mb-4">Tokens activos</h2>

        <div class="divide-y divide-slate-100">
            @forelse($tokens as $token)
                <div class="flex items-center justify-between py-3">
                    <div>
                        <div class="font-semibold text-slate-800 text-sm">{{ $token->name }}</div>
                        <div class="text-xs text-slate-500">
                            Creado {{ $token->created_at->format('d/m/Y H:i') }}
                            @if($token->last_used_at)
                                · Último uso {{ $token->last_used_at->format('d/m/Y H:i') }}
                            @else
                                · Nunca usado
                            @endif
                        </div>
                    </div>
                    <form method="POST" action="{{ route('api-tokens.destroy', $token->id) }}" onsubmit="return confirm('¿Revocar este token? Cualquier sistema que lo use dejará de poder conectarse.')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 text-sm font-semibold">Revocar</button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-slate-400 py-2">Todavía no tienes tokens creados.</p>
            @endforelse
        </div>

        <form method="POST" action="{{ route('api-tokens.store') }}" class="flex gap-2 mt-5 pt-5 border-t border-slate-100">
            @csrf
            <input type="text" name="name" required placeholder="Nombre del token (ej. SAP Producción)"
                   class="flex-1 rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <button class="rounded-lg bg-brand-600 text-white px-5 py-2 text-sm font-semibold hover:bg-brand-700 shrink-0">
                Generar token
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
        <h2 class="font-bold text-slate-900">Cómo usarlo</h2>
        <p class="text-sm text-slate-600">
            Con el token, cualquier sistema externo puede enviar (o consultar) tu cartera vía HTTP, autenticando con
            <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs">Authorization: Bearer &lt;token&gt;</code>.
        </p>

        <div>
            <div class="text-xs font-bold text-slate-500 uppercase mb-1">Enviar una cuenta por cobrar</div>
            <pre class="bg-slate-900 text-slate-100 text-xs rounded-xl p-4 overflow-x-auto">curl -X POST {{ url('/api/receivables') }} \
  -H "Authorization: Bearer TU_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "Distribuidora Panama SA",
    "customer_email": "pagos@distpanama.test",
    "document_number": "FAC-100",
    "total": 500.00,
    "due_date": "2026-09-01"
  }'</pre>
        </div>

        <div>
            <div class="text-xs font-bold text-slate-500 uppercase mb-1">Enviar una cuenta por pagar</div>
            <pre class="bg-slate-900 text-slate-100 text-xs rounded-xl p-4 overflow-x-auto">curl -X POST {{ url('/api/payables') }} \
  -H "Authorization: Bearer TU_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "vendor_name": "Proveedora Nacional SA",
    "document_number": "CXP-100",
    "total": 200.00,
    "due_date": "2026-09-05"
  }'</pre>
        </div>

        <div>
            <div class="text-xs font-bold text-slate-500 uppercase mb-1">Consultar cartera</div>
            <pre class="bg-slate-900 text-slate-100 text-xs rounded-xl p-4 overflow-x-auto">curl {{ url('/api/receivables') }} -H "Authorization: Bearer TU_TOKEN" -H "Accept: application/json"
curl {{ url('/api/payables') }} -H "Authorization: Bearer TU_TOKEN" -H "Accept: application/json"</pre>
        </div>

        <p class="text-xs text-slate-500">
            El envío es idempotente por <code class="bg-slate-100 px-1 rounded">document_number</code>: reenviar el mismo documento actualiza en vez de duplicar — igual que la importación por CSV.
        </p>
    </div>
</div>
</x-app-layout>
