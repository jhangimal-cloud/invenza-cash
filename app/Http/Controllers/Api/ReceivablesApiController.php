<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Receivable;
use Illuminate\Http\Request;

class ReceivablesApiController extends Controller
{
    public function index(Request $request)
    {
        $receivables = Receivable::where('company_id', $request->user()->company_id)
            ->where('balance', '>', 0)
            ->where('status', '!=', 'PAGADO')
            ->orderBy('due_date')
            ->get([
                'id', 'customer_name', 'customer_email', 'document_number',
                'external_reference', 'total', 'balance', 'due_date', 'status',
            ]);

        return response()->json(['data' => $receivables]);
    }

    /**
     * Crea o actualiza (upsert por document_number) una cuenta por cobrar
     * empujada por un sistema externo. Mismo criterio de idempotencia que
     * el importador CSV.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:180'],
            'customer_email' => ['nullable', 'email', 'max:180'],
            'document_number' => ['required', 'string', 'max:80'],
            'external_reference' => ['nullable', 'string', 'max:120'],
            'total' => ['required', 'numeric', 'min:0'],
            'balance' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
        ]);

        $companyId = $request->user()->company_id;

        $receivable = Receivable::updateOrCreate(
            ['company_id' => $companyId, 'document_number' => $validated['document_number']],
            [
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? null,
                'external_reference' => $validated['external_reference'] ?? null,
                'total' => $validated['total'],
                'balance' => $validated['balance'] ?? $validated['total'],
                'due_date' => $validated['due_date'] ?? null,
                'status' => 'PENDIENTE',
            ]
        );

        return response()->json(['data' => $receivable], 201);
    }
}
