@php $rtl = app()->getLocale() === 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Setting::getValue($rtl ? 'platform_name_ar' : 'platform_name_en', 'Appreciation Platform') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { brand: { DEFAULT: '#004137', dark: '#00281E' } } } } };</script>
</head>
<body class="min-h-screen bg-gradient-to-br from-brand to-brand-dark flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-white/10 items-center justify-center text-3xl mb-3">★</div>
            <h1 class="text-2xl font-bold text-white">
                {{ \App\Models\Setting::getValue($rtl ? 'platform_name_ar' : 'platform_name_en', 'Appreciation Platform') }}
            </h1>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-8">
            @yield('content')
        </div>
        <div class="text-center mt-4 text-white/70 text-sm">
            <a href="{{ route('lang.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="hover:text-white">
                {{ app()->getLocale() === 'ar' ? 'English' : 'عربي' }}
            </a>
        </div>
    </div>
</body>
</html>
