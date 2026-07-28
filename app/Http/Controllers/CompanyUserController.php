<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class CompanyUserController extends Controller
{
    /**
     * Usuarios de mi empresa. Cualquier usuario ya logueado puede agregar
     * otro para su mismo company_id (sin roles/permisos todavia, MVP).
     */
    public function index(Request $request): View
    {
        $company = $request->user()->company;

        $users = User::where('company_id', $company->id)->orderBy('name')->get();
        $maxUsers = $company->maxUsers();

        return view('company.users.index', compact('users', 'maxUsers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $company = $request->user()->company;
        $companyId = $company->id;

        $currentCount = User::where('company_id', $companyId)->count();

        if ($currentCount >= $company->maxUsers()) {
            return back()->with('error', "Ya alcanzaste el límite de {$company->maxUsers()} usuarios de tu plan. Contacta a soporte para contratar usuarios adicionales.");
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'company_id' => $companyId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('company.users.index')->with('success', 'Usuario agregado correctamente. Ya puede iniciar sesión con su correo y la contraseña que le asignaste.');
    }
}
