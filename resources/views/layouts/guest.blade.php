<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Invenza Cash') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
            <div class="hidden lg:flex flex-col justify-between bg-gradient-to-br from-ink-950 via-ink-900 to-ink-800 text-white p-12 relative overflow-hidden">
                <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-brand-500/10"></div>
                <div class="absolute bottom-0 -left-16 w-72 h-72 rounded-full bg-white/5"></div>
                <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 24px 24px;"></div>

                <div class="relative">
                    <div class="inline-flex bg-white rounded-2xl px-4 py-3 shadow-xl shadow-black/20">
                        <img src="{{ asset('images/logo-cash.png') }}" alt="Cash by Invenza" class="h-8 w-auto">
                    </div>
                </div>

                <div class="relative max-w-md">
                    <h1 class="text-3xl font-extrabold leading-tight mb-4">
                        Claridad total sobre tu cartera y tu flujo de caja.
                    </h1>
                    <p class="text-slate-300 text-sm leading-relaxed mb-8">
                        Diseñado para contadores y equipos financieros que necesitan gestionar cobros,
                        proyectar ingresos y conectarse con cualquier sistema contable — sin depender
                        de uno solo.
                    </p>

                    <ul class="space-y-3">
                        <li class="flex items-center gap-3 text-sm text-slate-200">
                            <span class="w-5 h-5 rounded-full bg-brand-500/20 text-brand-300 flex items-center justify-center shrink-0">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            </span>
                            Bandeja de gestión de cobros con bitácora de actividad
                        </li>
                        <li class="flex items-center gap-3 text-sm text-slate-200">
                            <span class="w-5 h-5 rounded-full bg-brand-500/20 text-brand-300 flex items-center justify-center shrink-0">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            </span>
                            Proyección mensual de ingresos y Cash Flow
                        </li>
                        <li class="flex items-center gap-3 text-sm text-slate-200">
                            <span class="w-5 h-5 rounded-full bg-brand-500/20 text-brand-300 flex items-center justify-center shrink-0">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            </span>
                            Conector API para importar desde tu sistema contable
                        </li>
                    </ul>
                </div>

                <div class="relative text-xs text-slate-400">
                    &copy; {{ date('Y') }} Invenza. Todos los derechos reservados.
                </div>
            </div>

            <div class="flex flex-col items-center justify-center px-6 py-12 bg-slate-50">
                <div class="w-full max-w-sm">
                    <div class="lg:hidden flex items-center justify-center mb-8">
                        <img src="{{ asset('images/logo-cash.png') }}" alt="Cash by Invenza" class="h-10 w-auto">
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/60 p-8">
                        {{ $slot }}
                    </div>

                    <p class="text-center text-xs text-slate-400 mt-6">
                        Un producto de Invenza — hecho para profesionales de la contabilidad.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
