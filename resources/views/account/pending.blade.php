<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Invenza Cash') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased bg-slate-50">
        <div class="min-h-screen flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-md text-center">
                <div class="inline-flex bg-white rounded-2xl px-4 py-3 shadow-lg shadow-slate-200 mb-8">
                    <img src="{{ asset('images/logo-cash.png') }}" alt="Cash by Invenza" class="h-8 w-auto">
                </div>

                <div class="bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/60 p-8">
                    @if (auth()->user()?->company?->status === 'suspended')
                        <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008M4.5 19.5h15a1.5 1.5 0 001.3-2.25l-7.5-13a1.5 1.5 0 00-2.6 0l-7.5 13a1.5 1.5 0 001.3 2.25z" /></svg>
                        </div>
                        <h1 class="text-xl font-bold text-ink-900 mb-2">Cuenta suspendida</h1>
                        <p class="text-sm text-slate-500 leading-relaxed">
                            El acceso de tu empresa a Cash by Invenza está suspendido. Contacta a nuestro
                            equipo para resolver tu situación y reactivar el servicio.
                        </p>
                    @else
                        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-4">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" /><circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </div>
                        <h1 class="text-xl font-bold text-ink-900 mb-2">Tu registro está en revisión</h1>
                        <p class="text-sm text-slate-500 leading-relaxed">
                            Recibimos tu registro en Cash by Invenza. Nuestro equipo va a validar tu cuenta
                            y te contactaremos muy pronto para activar el acceso.
                        </p>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="mt-6">
                        @csrf
                        <button type="submit" class="text-sm text-slate-500 hover:text-ink-900 hover:underline">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
