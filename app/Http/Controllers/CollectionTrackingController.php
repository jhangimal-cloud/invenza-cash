<?php

namespace App\Http\Controllers;

use App\Mail\CollectionReminderMail;
use App\Models\CollectionTracking;
use App\Models\CollectionTrackingActivity;
use App\Models\CollectionTrackingStatus;
use App\Models\Payable;
use App\Models\Receivable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class CollectionTrackingController extends Controller
{
    /**
     * Bandeja: todas las cuentas por cobrar importadas con saldo pendiente,
     * cada una con su gestion (si ya se creo). A diferencia de Invenza (que
     * unifica dos fuentes), aca `receivables` ya es la unica fuente.
     */
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $statuses = CollectionTrackingStatus::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $users = User::where('company_id', $companyId)->orderBy('name')->get(['id', 'name']);

        $query = Receivable::with(['tracking.status', 'tracking.responsible'])
            ->where('company_id', $companyId)
            ->where('balance', '>', 0)
            ->where('status', '!=', 'PAGADO');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_id')) {
            $statusId = (int) $request->status_id;
            $query->whereHas('tracking', fn ($q) => $q->where('status_id', $statusId));
        }

        $receivables = $query->get()->map(function (Receivable $receivable) {
            $dueDate = $receivable->due_date;
            $daysOverdue = 0;

            if ($dueDate && $dueDate->isPast()) {
                $daysOverdue = now()->startOfDay()->diffInDays($dueDate->copy()->startOfDay());
            }

            return [
                'receivable' => $receivable,
                'tracking' => $receivable->tracking,
                'days_overdue' => $daysOverdue,
            ];
        })->sortByDesc('days_overdue')->values();

        $summary = [
            'total_open' => $receivables->count(),
            'total_balance' => $receivables->sum(fn ($row) => (float) $row['receivable']->balance),
            'due_today' => $receivables->filter(function ($row) {
                $next = $row['tracking']?->next_follow_up_at;

                return $next && $next->toDateString() <= now()->toDateString();
            })->count(),
        ];

        $alerts = $this->buildAlerts($companyId);

        return view('collections.index', compact('receivables', 'statuses', 'users', 'summary', 'alerts'));
    }

    /**
     * Panel de alertas de la bandeja, calculado en vivo cada vez que el
     * usuario entra (sin cron/scheduler). Siempre sobre TODA la cartera
     * abierta de la empresa, sin importar los filtros de busqueda activos.
     */
    private function buildAlerts(int $companyId): array
    {
        $today = now()->startOfDay();

        $openReceivables = Receivable::with('tracking')
            ->where('company_id', $companyId)
            ->where('balance', '>', 0)
            ->where('status', '!=', 'PAGADO')
            ->get();

        $newlyOverdue = $openReceivables->filter(
            fn (Receivable $r) => $r->due_date && $r->due_date->isSameDay($today->copy()->subDay())
        )->values();

        $noTrackingOverdue = $openReceivables->filter(
            fn (Receivable $r) => $r->tracking === null && $r->due_date && $r->due_date->lt($today)
        )->values();

        $trackingIds = $openReceivables->pluck('tracking.id')->filter()->values();

        $latestPromises = CollectionTrackingActivity::where('company_id', $companyId)
            ->where('activity_type', 'promise_payment')
            ->whereIn('tracking_id', $trackingIds)
            ->whereNotNull('promised_payment_date')
            ->orderByDesc('activity_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('tracking_id')
            ->map(fn ($group) => $group->first());

        $promisesToday = collect();
        $promisesOverdue = collect();

        foreach ($openReceivables as $receivable) {
            $trackingId = $receivable->tracking?->id;

            if (! $trackingId) {
                continue;
            }

            $promise = $latestPromises->get($trackingId);

            if (! $promise || ! $promise->promised_payment_date) {
                continue;
            }

            if ($promise->promised_payment_date->isSameDay($today)) {
                $promisesToday->push(['receivable' => $receivable, 'promise' => $promise]);
            } elseif ($promise->promised_payment_date->lt($today)) {
                $promisesOverdue->push(['receivable' => $receivable, 'promise' => $promise]);
            }
        }

        return [
            'newly_overdue' => $newlyOverdue,
            'no_tracking_overdue' => $noTrackingOverdue,
            'promises_today' => $promisesToday->values(),
            'promises_overdue' => $promisesOverdue->values(),
        ];
    }

    /**
     * Proyeccion de ingresos por mes (clon de la Fase 2 de Invenza, adaptado
     * a una sola fuente). El mes de cada cuenta se asigna: 1) la promesa de
     * pago mas reciente de su gestion si tiene fecha futura o del mes
     * actual, 2) si no hay promesa, el mes de vencimiento SOLO si es
     * futuro, 3) si no hay promesa y ya esta vencida, bucket separado
     * "vencido sin gestion" (no se mezcla con los meses).
     */
    public function forecast(Request $request)
    {
        $companyId = $request->user()->company_id;

        $data = $this->incomeForecastData($companyId);

        return view('collections.forecast', $data);
    }

    private function incomeForecastData(int $companyId): array
    {
        $receivables = Receivable::with('tracking')
            ->where('company_id', $companyId)
            ->where('balance', '>', 0)
            ->where('status', '!=', 'PAGADO')
            ->get();

        $trackingIds = $receivables->pluck('tracking.id')->filter()->values();

        $latestPromises = CollectionTrackingActivity::where('company_id', $companyId)
            ->where('activity_type', 'promise_payment')
            ->whereIn('tracking_id', $trackingIds)
            ->whereNotNull('promised_payment_date')
            ->orderByDesc('activity_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('tracking_id')
            ->map(fn ($group) => $group->first());

        [$monthKeys, $monthLabels, $laterThreshold] = $this->monthWindow();

        $currentMonthStart = now()->startOfMonth();

        $rows = $receivables->map(function (Receivable $receivable) use ($latestPromises, $laterThreshold, $currentMonthStart) {
            $trackingId = $receivable->tracking?->id;
            $promise = $trackingId ? $latestPromises->get($trackingId) : null;

            $bucketDate = null;

            if ($promise && $promise->promised_payment_date) {
                $promiseMonth = $promise->promised_payment_date->copy()->startOfMonth();

                if ($promiseMonth->greaterThanOrEqualTo($currentMonthStart)) {
                    $bucketDate = $promiseMonth;
                }
            }

            if ($bucketDate === null && $receivable->due_date && $receivable->due_date->isFuture()) {
                $bucketDate = $receivable->due_date->copy()->startOfMonth();
            }

            $bucket = 'overdue_no_promise';

            if ($bucketDate !== null) {
                $bucket = $bucketDate->greaterThanOrEqualTo($laterThreshold) ? 'later' : $bucketDate->format('Y-m');
            }

            return [
                'receivable' => $receivable,
                'tracking' => $receivable->tracking,
                'promised_payment_date' => $promise?->promised_payment_date,
                'bucket' => $bucket,
            ];
        });

        $bucketOrder = array_merge(['overdue_no_promise'], $monthKeys, ['later']);

        $buckets = collect($bucketOrder)->mapWithKeys(function ($key) use ($rows) {
            $bucketRows = $rows->where('bucket', $key)->values();

            return [$key => [
                'total' => $bucketRows->sum(fn ($row) => (float) $row['receivable']->balance),
                'count' => $bucketRows->count(),
            ]];
        });

        $bucketPositions = array_flip($bucketOrder);
        $rows = $rows->sortBy(fn ($row) => $bucketPositions[$row['bucket']] ?? 999)->values();

        return compact('rows', 'buckets', 'monthKeys', 'monthLabels');
    }

    /**
     * Ventana de 4 meses (mes actual + 3 siguientes) con etiquetas en
     * espanol. Compartida por incomeForecastData() y cashFlow() para que
     * ingresos y egresos usen exactamente los mismos meses.
     */
    private function monthWindow(): array
    {
        $spanishMonths = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        $monthKeys = [];
        $monthLabels = [];
        $cursor = now()->startOfMonth();

        for ($i = 0; $i < 4; $i++) {
            $key = $cursor->format('Y-m');
            $monthKeys[] = $key;
            $monthLabels[$key] = $spanishMonths[(int) $cursor->format('n')] . ' ' . $cursor->format('Y');
            $cursor = $cursor->copy()->addMonthNoOverflow();
        }

        return [$monthKeys, $monthLabels, $cursor];
    }

    /**
     * Cash Flow (egresos = Payable, solo por vencimiento, sin concepto de
     * promesa de pago del lado de egresos).
     */
    public function cashFlow(Request $request)
    {
        $companyId = $request->user()->company_id;

        $income = $this->incomeForecastData($companyId);
        [$monthKeys, $monthLabels, $laterThreshold] = $this->monthWindow();

        $expenseRows = Payable::where('company_id', $companyId)
            ->where('balance', '>', 0)
            ->where('status', '!=', 'PAGADO')
            ->get()
            ->map(function (Payable $payable) use ($laterThreshold) {
                $dueDate = $payable->due_date;
                $bucket = 'overdue';

                if ($dueDate && $dueDate->isFuture()) {
                    $bucketDate = $dueDate->copy()->startOfMonth();
                    $bucket = $bucketDate->greaterThanOrEqualTo($laterThreshold) ? 'later' : $bucketDate->format('Y-m');
                }

                return [
                    'payable' => $payable,
                    'bucket' => $bucket,
                ];
            });

        $expenseBucketOrder = array_merge(['overdue'], $monthKeys, ['later']);

        $expenseBuckets = collect($expenseBucketOrder)->mapWithKeys(function ($key) use ($expenseRows) {
            $bucketRows = $expenseRows->where('bucket', $key)->values();

            return [$key => [
                'total' => $bucketRows->sum(fn ($row) => (float) $row['payable']->balance),
                'count' => $bucketRows->count(),
            ]];
        });

        $expenseBucketPositions = array_flip($expenseBucketOrder);
        $expenseRows = $expenseRows->sortBy(fn ($row) => $expenseBucketPositions[$row['bucket']] ?? 999)->values();

        $netByMonth = collect($monthKeys)->mapWithKeys(fn ($key) => [
            $key => $income['buckets'][$key]['total'] - $expenseBuckets[$key]['total'],
        ]);
        $netByMonth['later'] = $income['buckets']['later']['total'] - $expenseBuckets['later']['total'];

        return view('collections.cashflow', [
            'monthKeys' => $monthKeys,
            'monthLabels' => $monthLabels,
            'incomeBuckets' => $income['buckets'],
            'expenseBuckets' => $expenseBuckets,
            'netByMonth' => $netByMonth,
            'expenseRows' => $expenseRows,
        ]);
    }

    public function createFromReceivable(Request $request, Receivable $receivable)
    {
        $companyId = $request->user()->company_id;

        abort_if((int) $receivable->company_id !== (int) $companyId, 403);

        $existing = CollectionTracking::where('receivable_id', $receivable->id)->first();

        if ($existing) {
            return redirect()->route('collections.show', $existing)
                ->with('success', 'Esta cuenta ya tenía una gestión. Se abrió la existente.');
        }

        $initialStatusId = CollectionTrackingStatus::where('company_id', $companyId)
            ->where('is_active', true)
            ->where('is_initial', true)
            ->value('id');

        $tracking = null;

        DB::transaction(function () use (&$tracking, $companyId, $receivable, $initialStatusId, $request) {
            $tracking = CollectionTracking::create([
                'company_id' => $companyId,
                'receivable_id' => $receivable->id,
                'responsible_user_id' => $request->user()->id,
                'status_id' => $initialStatusId,
                'title' => 'CxC ' . ($receivable->document_number ?: ('#' . $receivable->id)),
                'balance_amount' => $receivable->balance,
                'priority' => 'normal',
                'original_due_date' => $receivable->due_date,
                'next_follow_up_at' => now()->addDay(),
                'last_activity_at' => now(),
                'is_active' => true,
            ]);

            CollectionTrackingActivity::create([
                'company_id' => $companyId,
                'tracking_id' => $tracking->id,
                'user_id' => $request->user()->id,
                'activity_type' => 'system',
                'direction' => 'internal',
                'subject' => 'Gestión de cobro creada',
                'body' => 'Se creó la gestión de cobro para ' . $receivable->customer_name . '.',
                'activity_at' => now(),
                'next_follow_up_at' => $tracking->next_follow_up_at,
            ]);
        });

        return redirect()->route('collections.show', $tracking)
            ->with('success', 'Gestión de cobro creada correctamente.');
    }

    public function show(Request $request, CollectionTracking $collectionTracking)
    {
        $companyId = $request->user()->company_id;

        abort_if((int) $collectionTracking->company_id !== (int) $companyId, 403);

        $collectionTracking->load(['status', 'responsible', 'receivable']);

        $statuses = CollectionTrackingStatus::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $activities = CollectionTrackingActivity::with(['user', 'oldStatus', 'newStatus'])
            ->where('tracking_id', $collectionTracking->id)
            ->orderByDesc('activity_at')
            ->orderByDesc('id')
            ->get();

        return view('collections.show', compact('collectionTracking', 'statuses', 'activities'));
    }

    public function addActivity(Request $request, CollectionTracking $collectionTracking)
    {
        $companyId = $request->user()->company_id;

        abort_if((int) $collectionTracking->company_id !== (int) $companyId, 403);

        $validated = $request->validate([
            'activity_type' => [
                'required',
                Rule::in(['note', 'call', 'whatsapp', 'email_sent', 'email_received', 'meeting', 'status_change', 'reminder', 'promise_payment', 'system']),
            ],
            'subject' => ['nullable', 'string', 'max:220'],
            'body' => ['nullable', 'string'],
            'promised_amount' => ['nullable', 'numeric', 'min:0'],
            'promised_payment_date' => ['nullable', 'date'],
            'new_status_id' => [
                'nullable',
                Rule::exists('collection_tracking_statuses', 'id')->where(fn ($q) => $q->where('company_id', $companyId)->whereNull('deleted_at')),
            ],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($validated, $collectionTracking, $companyId, $request) {
            $oldStatusId = $collectionTracking->status_id;
            $newStatusId = $validated['new_status_id'] ?? null;

            $direction = match ($validated['activity_type']) {
                'email_sent' => 'outbound',
                'email_received' => 'inbound',
                default => 'internal',
            };

            CollectionTrackingActivity::create([
                'company_id' => $companyId,
                'tracking_id' => $collectionTracking->id,
                'user_id' => $request->user()->id,
                'activity_type' => $validated['activity_type'],
                'direction' => $direction,
                'old_status_id' => $newStatusId ? $oldStatusId : null,
                'new_status_id' => $newStatusId,
                'subject' => $validated['subject'] ?? null,
                'body' => $validated['body'] ?? null,
                'promised_amount' => $validated['promised_amount'] ?? null,
                'promised_payment_date' => $validated['promised_payment_date'] ?? null,
                'activity_at' => now(),
                'next_follow_up_at' => $validated['next_follow_up_at'] ?? null,
            ]);

            $updates = ['last_activity_at' => now()];

            if (array_key_exists('next_follow_up_at', $validated)) {
                $updates['next_follow_up_at'] = $validated['next_follow_up_at'];
            }

            if ($newStatusId) {
                $updates['status_id'] = $newStatusId;
                $newStatus = CollectionTrackingStatus::find($newStatusId);
                $updates['closed_at'] = $newStatus?->is_final ? now() : null;

                if ($newStatus?->is_final && $newStatus->name === 'Pagado') {
                    $collectionTracking->receivable()->update(['status' => 'PAGADO', 'balance' => 0]);
                }
            }

            $collectionTracking->update($updates);
        });

        return redirect()->route('collections.show', $collectionTracking)
            ->with('success', 'Actividad registrada correctamente.');
    }

    public function sendReminder(Request $request, CollectionTracking $collectionTracking)
    {
        $companyId = $request->user()->company_id;

        abort_if((int) $collectionTracking->company_id !== (int) $companyId, 403);

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $collectionTracking->loadMissing('receivable');
        $email = trim((string) $collectionTracking->receivable->customer_email);

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return back()->with('error', 'Este cliente no tiene un correo electrónico válido registrado.');
        }

        $company = $request->user()->company;
        $customMessage = $validated['message'] ?? null;

        try {
            Mail::to($email)->send(new CollectionReminderMail($collectionTracking, $company, $customMessage));
        } catch (\Throwable $e) {
            Log::error('[COLLECTION REMINDER MAIL] Error enviando recordatorio de cobro', [
                'collection_tracking_id' => $collectionTracking->id,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'No se pudo enviar el recordatorio. Revise la configuración de correo o intente nuevamente.');
        }

        CollectionTrackingActivity::create([
            'company_id' => $companyId,
            'tracking_id' => $collectionTracking->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'reminder',
            'direction' => 'outbound',
            'subject' => 'Recordatorio enviado por correo',
            'body' => $customMessage,
            'activity_at' => now(),
        ]);

        $collectionTracking->update(['last_activity_at' => now()]);

        return redirect()->route('collections.show', $collectionTracking)
            ->with('success', 'Recordatorio enviado correctamente a ' . $email . '.');
    }
}
