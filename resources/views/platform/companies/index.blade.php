<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Panel Invenza — Empresas</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased bg-slate-50">
        <div class="min-h-screen px-6 py-10">
            <div class="max-w-5xl mx-auto">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-xl font-bold text-ink-900">Panel de Invenza — Empresas</h1>
                        <p class="text-sm text-slate-500 mt-1">Aprueba, revisa o suspende el acceso de cada empresa registrada en Cash by Invenza.</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-slate-500 hover:text-ink-900 hover:underline">Cerrar sesión</button>
                    </form>
                </div>

                @if (session('status'))
                    <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
                            <tr>
                                <th class="text-left px-5 py-3">Empresa</th>
                                <th class="text-left px-5 py-3">Contacto</th>
                                <th class="text-left px-5 py-3">Usuarios / Cupo</th>
                                <th class="text-left px-5 py-3">IA</th>
                                <th class="text-left px-5 py-3">Registrada</th>
                                <th class="text-left px-5 py-3">Estado</th>
                                <th class="text-right px-5 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($companies as $company)
                                <tr>
                                    <td class="px-5 py-4 font-medium text-slate-800">{{ $company->name }}</td>
                                    <td class="px-5 py-4 text-slate-500">{{ $company->contact_email }}</td>
                                    <td class="px-5 py-4 text-slate-500">
                                        <form method="POST" action="{{ route('platform.companies.max-users', $company) }}" class="flex items-center gap-1.5">
                                            @csrf
                                            <span>{{ $company->users_count }} /</span>
                                            <input type="number" name="max_users" min="1" max="1000" value="{{ $company->maxUsers() }}"
                                                   class="w-16 rounded-lg border-slate-300 text-sm py-1 px-2 focus:border-brand-500 focus:ring-brand-500">
                                            <button type="submit" class="text-xs font-semibold text-brand-700 hover:text-brand-800 hover:underline">Guardar</button>
                                        </form>
                                    </td>
                                    <td class="px-5 py-4 text-slate-500">
                                        <form method="POST" action="{{ route('platform.companies.intelligence', $company) }}" class="flex items-center gap-1.5">
                                            @csrf
                                            <input type="checkbox" name="intelligence_enabled" value="1" @checked($company->intelligence_enabled)
                                                   class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                            <input type="number" step="0.01" min="0" name="intelligence_monthly_budget_usd"
                                                   value="{{ $company->intelligence_monthly_budget_usd }}" placeholder="USD/mes"
                                                   class="w-20 rounded-lg border-slate-300 text-sm py-1 px-2 focus:border-brand-500 focus:ring-brand-500">
                                            <button type="submit" class="text-xs font-semibold text-brand-700 hover:text-brand-800 hover:underline">Guardar</button>
                                        </form>
                                    </td>
                                    <td class="px-5 py-4 text-slate-500">{{ $company->created_at->format('d/m/Y') }}</td>
                                    <td class="px-5 py-4">
                                        @if ($company->status === 'active')
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-semibold">Activa</span>
                                        @elseif ($company->status === 'suspended')
                                            <span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2.5 py-1 text-xs font-semibold">Suspendida</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2.5 py-1 text-xs font-semibold">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right space-x-2">
                                        @if ($company->status !== 'active')
                                            <form method="POST" action="{{ route('platform.companies.approve', $company) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-brand-700 hover:text-brand-800 font-semibold hover:underline">Aprobar</button>
                                            </form>
                                        @endif
                                        @if ($company->status !== 'suspended')
                                            <form method="POST" action="{{ route('platform.companies.suspend', $company) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold hover:underline">Suspender</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-8 text-center text-slate-400">Todavía no hay empresas registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>
