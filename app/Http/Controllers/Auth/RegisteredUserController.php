<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NewCompanyRegistered;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:180'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($request) {
            $company = Company::create([
                'name' => $request->company_name,
                'contact_email' => $request->email,
            ]);

            $this->seedDefaultStatuses($company->id);

            return User::create([
                'company_id' => $company->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        });

        event(new Registered($user));

        try {
            Mail::to(config('platform.admin_email'))->send(new NewCompanyRegistered($user->company, $user));
        } catch (\Throwable $e) {
            Log::error('No se pudo enviar el correo de nuevo registro pendiente: '.$e->getMessage());
        }

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    private function seedDefaultStatuses(int $companyId): void
    {
        $defaults = [
            ['name' => 'Pendiente de gestión', 'color' => '#2563EB', 'sort_order' => 10, 'is_initial' => true,  'is_final' => false, 'stops_notifications' => false],
            ['name' => 'En gestión', 'color' => '#F59E0B', 'sort_order' => 20, 'is_initial' => false, 'is_final' => false, 'stops_notifications' => false],
            ['name' => 'Promesa de pago', 'color' => '#8B5CF6', 'sort_order' => 30, 'is_initial' => false, 'is_final' => false, 'stops_notifications' => false],
            ['name' => 'Pagado', 'color' => '#16A34A', 'sort_order' => 40, 'is_initial' => false, 'is_final' => true, 'stops_notifications' => true],
            ['name' => 'Incobrable / legal', 'color' => '#DC2626', 'sort_order' => 50, 'is_initial' => false, 'is_final' => true, 'stops_notifications' => true],
        ];

        foreach ($defaults as $status) {
            DB::table('collection_tracking_statuses')->insert(array_merge($status, [
                'company_id' => $companyId,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
