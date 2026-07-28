<x-app-layout>
<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Usuarios de mi empresa</h1>
        <p class="text-sm text-slate-500 mt-0.5">
            Agrega compañeros de tu equipo para que entren con su propio correo y vean la misma cartera.
        </p>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-slate-900">Usuarios activos</h2>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $users->count() >= $maxUsers ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600' }}">
                {{ $users->count() }} de {{ $maxUsers }} usuarios de tu plan
            </span>
        </div>

        <div class="divide-y divide-slate-100">
            @foreach($users as $user)
                <div class="flex items-center gap-3 py-3">
                    <div class="w-9 h-9 rounded-full bg-brand-500/10 text-brand-700 flex items-center justify-center text-xs font-bold shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="font-semibold text-slate-800 text-sm truncate">
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                                <span class="text-xs font-normal text-slate-400">(tú)</span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-500 truncate">{{ $user->email }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($users->count() >= $maxUsers)
            <div class="mt-5 pt-5 border-t border-slate-100">
                <div class="rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3">
                    Ya usaste los {{ $maxUsers }} usuarios incluidos en tu plan. Si necesitas agregar más, contáctanos para contratar usuarios adicionales.
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('company.users.store') }}" class="mt-5 pt-5 border-t border-slate-100 space-y-4">
                @csrf
                <h3 class="text-sm font-bold text-slate-800">Agregar usuario</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="name" value="Nombre" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" value="Correo" />
                        <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password" value="Contraseña" />
                        <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" value="Confirmar contraseña" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" required />
                    </div>
                </div>

                <x-primary-button>Agregar usuario</x-primary-button>
            </form>
        @endif
    </div>
</div>
</x-app-layout>
