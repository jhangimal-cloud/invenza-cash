<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payable;
use Illuminate\Http\Request;

class PayablesApiController extends Controller
{
    public function index(Request $request)
    {
        $payables = Payable::where('company_id', $request->user()->company_id)
            ->where('balance', '>', 0)
            ->where('status', '!=', 'PAGADO')
            ->orderBy('due_date')
            ->get([
                'id', 'vendor_name', 'vendor_email', 'document_number',
                'external_reference', 'total', 'balance', 'due_date', 'status',
            ]);

        return response()->json(['data' => $payables]);
    }

    /**
     * Crea o actualiza (upsert por document_number) una cuenta por pagar
     * empujada por un sistema externo.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_name' => ['required', 'string', 'max:180'],
            'vendor_email' => ['nullable', 'email', 'max:180'],
            'document_number' => ['required', 'string', 'max:80'],
            'external_reference' => ['nullable', 'string', 'max:120'],
            'total' => ['required', 'numeric', 'min:0'],
            'balance' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
        ]);

        $companyId = $request->user()->company_id;

        $payable = Payable::updateOrCreate(
            ['company_id' => $companyId, 'document_number' => $validated['document_number']],
            [
                'vendor_name' => $validated['vendor_name'],
                'vendor_email' => $validated['vendor_email'] ?? null,
                'external_reference' => $validated['external_reference'] ?? null,
                'total' => $validated['total'],
                'balance' => $validated['balance'] ?? $validated['total'],
                'due_date' => $validated['due_date'] ?? null,
                'status' => 'PENDIENTE',
            ]
        );

        return response()->json(['data' => $payable], 201);
    }
}
