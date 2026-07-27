@php
    $navItems = [
        ['route' => 'collections.index', 'label' => 'Gestión de cobros', 'pattern' => 'collections.*', 'icon' => 'inbox'],
        ['route' => 'receivables.import', 'label' => 'Importar cartera', 'pattern' => 'receivables.*', 'icon' => 'upload'],
    ];
@endphp

<aside class="flex flex-col w-64 shrink-0 bg-slate-950 text-slate-300 min-h-screen">
    <div class="flex items-center gap-3 px-6 py-6 border-b border-white/10">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-black text-sm shadow-lg shadow-brand-900/40">
            IC
        </div>
        <div>
            <div class="text-white font-bold text-sm leading-tight">Invenza Cash</div>
            <div class="text-slate-500 text-[11px] leading-tight">CRM de cobros</div>
        </div>
    </div>

    <nav class="flex-1 px-3 py-6 space-y-1">
        @foreach($navItems as $item)
            @php($isActive = request()->routeIs($item['pattern']))
            <a href="{{ route($item['route']) }}"
               class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                      {{ $isActive ? 'bg-brand-600 text-white shadow-lg shadow-brand-900/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                <span class="w-5 h-5 shrink-0 flex items-center justify-center">
                    @if($item['icon'] === 'inbox')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h4.5l1.5 3h6l1.5-3H21" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.5 6h13l2 6.5V18a1.5 1.5 0 01-1.5 1.5h-14A1.5 1.5 0 013.5 18v-5.5L5.5 6z" />
                        </svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0l-4 4m4-4l4 4" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2.5A1.5 1.5 0 005.5 20h13a1.5 1.5 0 001.5-1.5V16" />
                        </svg>
                    @endif
                </span>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="border-t border-white/10 p-3">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.outside="open = false"
                    class="w-full flex items-center gap-3 rounded-xl px-3 py-2.5 text-left hover:bg-white/5 transition">
                <div class="w-8 h-8 rounded-full bg-brand-500/20 text-brand-300 flex items-center justify-center text-xs font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</div>
                    <div class="text-[11px] text-slate-500 truncate">{{ Auth::user()->company?->name }}</div>
                </div>
            </button>

            <div x-show="open" x-cloak
                 class="absolute bottom-full left-0 mb-2 w-full rounded-xl bg-white shadow-xl border border-slate-200 py-1 overflow-hidden">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Perfil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
