@php
    $rtl = app()->getLocale() === 'ar';
    $platformName = $rtl
        ? \App\Models\Setting::getValue('platform_name_ar', 'منصة التقدير')
        : \App\Models\Setting::getValue('platform_name_en', 'Appreciation Platform');
    $isAdmin = auth()->user()?->hasAnyRole(['admin', 'super-admin']);
    $isSuperAdmin = auth()->user()?->hasRole('super-admin');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $platformName }} — @yield('title', __('app.dashboard'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { DEFAULT: '#004137', dark: '#00281E', deep: '#000A0F' } } } } };
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
<div class="min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-64 shrink-0 bg-white border-e border-gray-200 hidden lg:flex flex-col">
        <div class="h-16 flex items-center gap-3 px-5 border-b border-gray-100">
            <div class="w-8 h-8 rounded-lg bg-brand flex items-center justify-center text-white font-bold">★</div>
            <span class="font-bold text-gray-900 truncate">{{ $platformName }}</span>
        </div>

        <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
            @php
                $nav = [
                    ['dashboard', __('app.dashboard'), '🏠'],
                    ['employees.index', __('app.find_employees'), '🔍'],
                    ['history', __('app.my_history'), '🕘'],
                ];
            @endphp
            @foreach ($nav as [$route, $label, $icon])
                <a href="{{ route($route) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                          {{ request()->routeIs($route) ? 'bg-brand text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span>{{ $icon }}</span> {{ $label }}
                </a>
            @endforeach

            @if ($isAdmin)
                <p class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('app.admin_panel') }}</p>
                <a href="{{ route('admin.settings.edit') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.settings.*') ? 'bg-brand text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span>⚙️</span> {{ __('app.platform_settings') }}
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.users.*') ? 'bg-brand text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span>👥</span> {{ __('app.user_management') }}
                </a>
                @if ($isSuperAdmin)
                    <a href="{{ route('admin.reasons.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.reasons.*') ? 'bg-brand text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        <span>🏷️</span> {{ __('app.appreciation_reasons') }}
                    </a>
                @endif
            @endif
        </nav>

        <div class="p-4 border-t border-gray-100">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-brand/10 text-brand flex items-center justify-center font-bold">
                    {{ mb_strtoupper(mb_substr(auth()->user()->display_name ?? '?', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->display_name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->department?->display_name }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-sm text-red-600 hover:bg-red-50 rounded-lg py-2 transition">{{ __('app.logout') }}</button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
            <h1 class="font-semibold text-gray-900">@yield('title', __('app.dashboard'))</h1>
            <div class="flex items-center gap-4 text-sm">
                <div class="flex items-center gap-1 text-gray-500">
                    <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'font-bold text-brand' : 'hover:text-gray-800' }}">EN</a>
                    <span>/</span>
                    <a href="{{ route('lang.switch', 'ar') }}" class="{{ app()->getLocale() === 'ar' ? 'font-bold text-brand' : 'hover:text-gray-800' }}">عربي</a>
                </div>
            </div>
        </header>

        <main class="flex-1 p-6">
            <div class="max-w-6xl mx-auto">
                @include('partials.flash')
                @yield('content')
            </div>
        </main>
    </div>
</div>
</body>
</html>
