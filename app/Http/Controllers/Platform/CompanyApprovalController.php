<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
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
}
