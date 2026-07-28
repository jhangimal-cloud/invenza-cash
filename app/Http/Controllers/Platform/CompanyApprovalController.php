<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyApprovalController extends Controller
{
    public function index(): View
    {
        $priority = ['pending' => 0, 'active' => 1, 'suspended' => 2];

        $companies = Company::withCount('users')
            ->latest()
            ->get()
            ->sortBy(fn (Company $company) => $priority[$company->status] ?? 3)
            ->values();

        return view('platform.companies.index', compact('companies'));
    }

    public function approve(Company $company): RedirectResponse
    {
        $company->update(['status' => 'active']);

        return back()->with('status', 'Empresa "'.$company->name.'" aprobada.');
    }

    public function suspend(Company $company): RedirectResponse
    {
        $company->update(['status' => 'suspended']);

        return back()->with('status', 'Empresa "'.$company->name.'" suspendida.');
    }

    public function updateMaxUsers(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'max_users' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        $company->update(['max_users' => $validated['max_users']]);

        return back()->with('status', 'Cupo de usuarios de "'.$company->name.'" actualizado a '.$validated['max_users'].'.');
    }
}
