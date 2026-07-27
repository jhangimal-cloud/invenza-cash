<x-app-layout>
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">Importar cartera por cobrar</h1>
            <p class="text-sm text-slate-500">Sube un CSV con tus cuentas por cobrar para empezar a gestionarlas.</p>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-4">
            <div class="rounded-lg bg-blue-50 border border-blue-100 text-blue-800 text-xs px-4 py-3">
                El archivo debe ser CSV, con encabezado, columnas: <strong>cliente, correo, documento, monto, vencimiento</strong>
                (correo y vencimiento son opcionales). El vencimiento puede ir en formato AAAA-MM-DD o DD/MM/AAAA.
                Reimportar el mismo archivo actualiza las cuentas existentes (por número de documento), no las duplica.
            </div>

            <form method="POST" action="{{ route('receivables.import.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Archivo CSV</label>
                    <input type="file" name="file" accept=".csv,.txt" required
                           class="block w-full text-sm border border-slate-300 rounded-lg px-3 py-2">
                    @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <button class="rounded-lg bg-indigo-600 text-white px-5 py-2.5 font-semibold text-sm hover:bg-indigo-700">
                    Importar
                </button>
            </form>
        </div>

        <a href="{{ route('collections.index') }}" class="text-sm text-slate-600 hover:text-slate-900 underline">
            Volver a la bandeja
        </a>
    </div>
</div>
</x-app-layout>
