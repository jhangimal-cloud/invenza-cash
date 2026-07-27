<x-guest-layout>
    <h2 class="text-xl font-bold text-slate-800 mb-1">Crea tu cuenta</h2>
    <p class="text-sm text-slate-500 mb-6">Empieza a gestionar tus cobros en minutos.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Company name -->
        <div>
            <x-input-label for="company_name" value="Nombre de la empresa" />
            <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" required autofocus />
            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
        </div>

        <!-- Name -->
        <div class="mt-4">
            <x-input-label for="name" value="Tu nombre" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" value="Correo" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar contraseña" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                Registrarme
            </x-primary-button>
        </div>
    </form>

    <p class="text-sm text-slate-500 mt-6 text-center">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}" class="text-brand-600 font-semibold hover:underline">Inicia sesión</a>
    </p>
</x-guest-layout>
