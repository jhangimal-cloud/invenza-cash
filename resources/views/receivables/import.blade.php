<x-app-layout>
<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Importar cartera por cobrar</h1>
        <p class="text-sm text-slate-500 mt-0.5">Sube un CSV con tus cuentas por cobrar para empezar a gestionarlas.</p>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-8 space-y-6">
        <div class="flex gap-3 rounded-xl bg-brand-50 border border-brand-100 text-brand-800 text-xs px-4 py-3">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-5 h-5 shrink-0">
                <circle cx="12" cy="12" r="9" />
                <path stroke-linecap="round" d="M12 8h.01M11 12h1v4h1" />
            </svg>
            <div>
                El archivo debe ser CSV, con encabezado, columnas: <strong>cliente, correo, documento, monto, vencimiento</strong>
                (correo y vencimiento son opcionales). El vencimiento puede ir en formato AAAA-MM-DD o DD/MM/AAAA.
                Reimportar el mismo archivo actualiza las cuentas existentes (por número de documento), no las duplica.
            </div>
        </div>

        <form method="POST" action="{{ route('receivables.import.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Archivo CSV</label>
                <label for="csvFile" class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-slate-300 rounded-xl px-6 py-10 text-center cursor-pointer hover:border-brand-400 hover:bg-brand-50/40 transition">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-8 h-8 text-brand-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l-4-4m4 4l4-4" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2.5A1.5 1.5 0 005.5 20h13a1.5 1.5 0 001.5-1.5V16" />
                    </svg>
                    <span class="text-sm font-semibold text-slate-700">Haz clic para elegir un archivo</span>
                    <span id="fileNameLabel" class="text-xs text-slate-400">CSV, hasta 5MB</span>
                    <input id="csvFile" type="file" name="file" accept=".csv,.txt" required class="hidden"
                           onchange="document.getElementById('fileNameLabel').textContent = this.files[0]?.name || 'CSV, hasta 5MB'">
                </label>
                @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <button class="w-full rounded-xl bg-brand-600 text-white px-5 py-3 font-semibold text-sm hover:bg-brand-700 shadow-sm shadow-brand-900/10">
                Importar
            </button>
        </form>
    </div>

    <a href="{{ route('collections.index') }}" class="inline-block text-sm text-slate-500 hover:text-slate-800">
        ← Volver a la bandeja
    </a>
</div>
</x-app-layout>
