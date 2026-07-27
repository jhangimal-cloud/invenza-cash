<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    /**
     * Pantalla para que el usuario cree/revoque tokens de API, usados por
     * sistemas externos (SAP, CONTPAQi, etc.) para empujar cartera via
     * /api/receivables y /api/payables.
     */
    public function index(Request $request)
    {
        $tokens = $request->user()->tokens()->orderByDesc('created_at')->get();

        return view('api-tokens.index', compact('tokens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $token = $request->user()->createToken($request->name);

        return redirect()->route('api-tokens.index')
            ->with('plainTextToken', $token->plainTextToken)
            ->with('success', 'Token creado. Cópialo ahora, no se podrá ver de nuevo.');
    }

    public function destroy(Request $request, int $tokenId)
    {
        $request->user()->tokens()->where('id', $tokenId)->delete();

        return redirect()->route('api-tokens.index')->with('success', 'Token revocado.');
    }
}
