<x-app-layout>
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ $collectionTracking->title }}</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $collectionTracking->tracking_code }}</p>
        </div>
        <a href="{{ route('collections.index') }}" class="rounded-xl bg-white border border-slate-300 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 font-medium">
            ← Volver a la bandeja
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
                <h2 class="font-bold text-slate-900">Resumen</h2>

                <div>
                    <div class="text-xs text-slate-500">Estado</div>
                    @if($collectionTracking->status)
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-white mt-1" style="background-color: {{ $collectionTracking->status->color }}">
                            {{ $collectionTracking->status->name }}
                        </span>
                    @else
                        <div class="text-slate-400">Sin estado</div>
                    @endif
                </div>

                <div>
                    <div class="text-xs text-slate-500">Cliente</div>
                    <div class="font-semibold text-slate-900">{{ $collectionTracking->receivable->customer_name }}</div>
                    @if($collectionTracking->receivable->customer_email)
                        <div class="text-sm text-slate-500">{{ $collectionTracking->receivable->customer_email }}</div>
                    @endif
                </div>

                <div>
                    <div class="text-xs text-slate-500">Responsable</div>
                    <div class="font-semibold text-slate-900">{{ $collectionTracking->responsible?->name ?: 'Sin responsable' }}</div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                    <div>
                        <div class="text-xs text-slate-500">Saldo</div>
                        <div class="font-bold text-slate-900 text-lg">${{ number_format((float) $collectionTracking->receivable->balance, 2) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500">Prioridad</div>
                        <div class="font-bold text-slate-900 text-lg">{{ ucfirst($collectionTracking->priority) }}</div>
                    </div>
                </div>

                <div>
                    <div class="text-xs text-slate-500">Próxima gestión</div>
                    <div class="{{ $collectionTracking->next_follow_up_at && $collectionTracking->next_follow_up_at->isPast() ? 'text-red-600 font-bold' : 'text-slate-800 font-semibold' }}">
                        {{ $collectionTracking->next_follow_up_at ? $collectionTracking->next_follow_up_at->format('d/m/Y H:i') : 'Sin fecha' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs text-slate-500">Vencimiento original</div>
                    <div class="{{ $collectionTracking->original_due_date && $collectionTracking->original_due_date->isPast() ? 'text-red-600 font-bold' : 'text-slate-800 font-semibold' }}">
                        {{ $collectionTracking->original_due_date ? $collectionTracking->original_due_date->format('d/m/Y') : 'Sin fecha' }}
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('collections.activities.store', $collectionTracking) }}" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
                @csrf
                <h2 class="font-bold text-slate-900">Registrar gestión</h2>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Tipo</label>
                    <select name="activity_type" id="activityType" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" required
                            onchange="document.getElementById('promiseFields').classList.toggle('hidden', this.value !== 'promise_payment')">
                        <option value="note">Nota</option>
                        <option value="call">Llamada</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="email_sent">Correo enviado</option>
                        <option value="email_received">Correo recibido</option>
                        <option value="meeting">Reunión</option>
                        <option value="promise_payment">Promesa de pago</option>
                        <option value="reminder">Recordatorio</option>
                        <option value="status_change">Cambio de estado</option>
                    </select>
                </div>

                <div id="promiseFields" class="hidden space-y-3 rounded-xl bg-violet-50 border border-violet-200 p-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Monto prometido</label>
                        <input type="number" step="0.01" min="0" name="promised_amount" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha prometida de pago</label>
                        <input type="date" name="promised_payment_date" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Cambiar estado</label>
                    <select name="new_status_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">Mantener estado actual</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Asunto</label>
                    <input type="text" name="subject" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Ej: Cliente dice que paga el viernes">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Detalle</label>
                    <textarea name="body" rows="4" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Detalle de la gestión realizada..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Próxima gestión</label>
                    <input type="datetime-local" name="next_follow_up_at" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>

                <button class="w-full rounded-xl bg-brand-600 text-white px-4 py-2.5 text-sm font-semibold hover:bg-brand-700 shadow-sm shadow-brand-900/10">Guardar gestión</button>
            </form>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="font-bold text-slate-900">Bitácora</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Historial de llamadas, notas, correos, reuniones, promesas de pago y cambios de estado.</p>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($activities as $activity)
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-2">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                                        {{ strtoupper(substr($activity->activity_type, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900">
                                            {{ $activity->subject ?: ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                                        </div>
                                        <div class="text-xs text-slate-500 mt-0.5">
                                            {{ $activity->activity_at ? $activity->activity_at->format('d/m/Y H:i') : $activity->created_at->format('d/m/Y H:i') }}
                                            · {{ $activity->user?->name ?: 'Sistema' }}
                                            · {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                                        </div>
                                    </div>
                                </div>
                                @if($activity->newStatus)
                                    <div class="text-xs text-slate-500">
                                        Estado:
                                        @if($activity->oldStatus){{ $activity->oldStatus->name }} →@endif
                                        <span class="font-semibold text-slate-800">{{ $activity->newStatus->name }}</span>
                                    </div>
                                @endif
                            </div>

                            @if($activity->body)
                                <div class="mt-3 ml-11 text-sm text-slate-700 whitespace-pre-line">{{ $activity->body }}</div>
                            @endif

                            @if($activity->promised_amount)
                                <div class="mt-3 ml-11 text-xs text-violet-700 font-semibold bg-violet-50 border border-violet-200 rounded-lg px-3 py-2 inline-block">
                                    Promesa de pago: ${{ number_format((float) $activity->promised_amount, 2) }}
                                    @if($activity->promised_payment_date)
                                        para el {{ $activity->promised_payment_date->format('d/m/Y') }}
                                    @endif
                                </div>
                            @endif

                            @if($activity->next_follow_up_at)
                                <div class="mt-3 ml-11 text-xs text-blue-700 font-semibold">
                                    Próxima gestión: {{ $activity->next_follow_up_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-10 text-center text-slate-500">No hay gestiones registradas.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
