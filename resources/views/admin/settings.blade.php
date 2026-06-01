@extends('layouts.app')
@section('title', __('app.platform_settings'))

@section('content')
    <div class="max-w-3xl space-y-6">

        {{-- Logo (separate multipart form) --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-4">{{ __('app.logo') }}</h3>
            <form method="POST" action="{{ route('admin.settings.logo') }}" enctype="multipart/form-data" class="flex items-center gap-4">
                @csrf
                <div class="w-20 h-20 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden">
                    @if (!empty($settings['logo_path']))
                        <img src="{{ asset('storage/' . $settings['logo_path']) }}" alt="logo" class="w-full h-full object-contain">
                    @else
                        <span class="text-gray-300 text-2xl">★</span>
                    @endif
                </div>
                <div class="space-y-2">
                    <input type="file" name="logo" accept="image/png,image/jpeg,image/svg+xml" required
                           class="block text-sm text-gray-600 file:me-3 file:rounded-lg file:border-0 file:bg-brand file:text-white file:px-3 file:py-1.5">
                    <p class="text-xs text-gray-400">{{ __('app.logo_hint') }}</p>
                    <button class="text-sm bg-gray-100 hover:bg-gray-200 rounded-lg px-3 py-1.5">{{ __('app.upload_logo') }}</button>
                </div>
            </form>
        </div>

        {{-- Main settings --}}
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-900 border-b border-gray-100 pb-3">{{ __('app.platform_identity') }}</h3>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.platform_name_en') }}</label>
                        <input name="platform_name_en" value="{{ old('platform_name_en', $settings['platform_name_en'] ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.platform_name_ar') }}</label>
                        <input name="platform_name_ar" dir="rtl" value="{{ old('platform_name_ar', $settings['platform_name_ar'] ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-900 border-b border-gray-100 pb-3">{{ __('app.theme_colors') }}</h3>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.primary_color') }}</label>
                        <input type="color" name="primary_color" value="{{ old('primary_color', $settings['primary_color'] ?? '#004137') }}" class="h-10 w-20 rounded border border-gray-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.secondary_color') }}</label>
                        <input type="color" name="secondary_color" value="{{ old('secondary_color', $settings['secondary_color'] ?? '#00281E') }}" class="h-10 w-20 rounded border border-gray-200">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-900 border-b border-gray-100 pb-3">{{ __('app.appreciation_rules') }}</h3>
                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.monthly_limit') }}</label>
                        <input type="number" name="monthly_appreciation_limit" min="1" max="100" value="{{ old('monthly_appreciation_limit', $settings['monthly_appreciation_limit'] ?? 10) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.daily_limit') }}</label>
                        <input type="number" name="max_daily_appreciations" min="1" max="50" value="{{ old('max_daily_appreciations', $settings['max_daily_appreciations'] ?? 5) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.same_receiver_limit') }}</label>
                        <input type="number" name="max_same_receiver_per_month" min="1" max="20" value="{{ old('max_same_receiver_per_month', $settings['max_same_receiver_per_month'] ?? 1) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-4">{{ __('app.notifications') }}</h3>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="email_notifications_enabled" value="1" class="rounded border-gray-300 text-brand focus:ring-brand"
                           {{ old('email_notifications_enabled', $settings['email_notifications_enabled'] ?? true) ? 'checked' : '' }}>
                    {{ __('app.email_notifications') }}
                </label>
            </div>

            <div class="flex justify-end">
                <button class="bg-brand hover:bg-brand-dark text-white font-medium rounded-lg px-6 py-2.5 transition">{{ __('app.save') }}</button>
            </div>
        </form>
    </div>
@endsection
