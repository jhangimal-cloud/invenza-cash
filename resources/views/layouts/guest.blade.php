<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
            <div class="hidden lg:flex flex-col justify-between bg-gradient-to-br from-brand-700 via-brand-600 to-indigo-900 text-white p-12 relative overflow-hidden">
                <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white/10"></div>
                <div class="absolute bottom-0 -left-16 w-72 h-72 rounded-full bg-white/5"></div>

                <div class="relative flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center font-black">IC</div>
                    <span class="font-bold text-lg">Invenza Cash</span>
                </div>

                <div class="relative max-w-md">
                    <h1 class="text-3xl font-extrabold leading-tight mb-4">
                        Gestiona tus cobros sin depender de un solo sistema contable.
                    </h1>
                    <p class="text-brand-100 text-sm leading-relaxed">
                        Importa tu cartera por cobrar, da seguimiento a cada gestión, registra promesas de
                        pago y ten claridad de cuánto vas a cobrar cada mes.
                    </p>
                </div>

                <div class="relative text-xs text-brand-200">
                    Un producto de Invenza
                </div>
            </div>

            <div class="flex flex-col items-center justify-center px-6 py-12 bg-slate-50">
                <div class="w-full max-w-sm">
                    <div class="lg:hidden flex items-center gap-3 mb-8 justify-center">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-black text-sm">IC</div>
                        <span class="font-bold text-lg text-slate-800">Invenza Cash</span>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
