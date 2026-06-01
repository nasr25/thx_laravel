@extends('layouts.guest')

@section('content')
    <h2 class="text-lg font-semibold text-gray-900 mb-1">{{ __('app.admin_sign_in') }}</h2>
    <p class="text-sm text-gray-500 mb-6">{{ __('app.admin_sign_in_subtitle') }}</p>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-3 py-2 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.username') }}</label>
            <input name="username" value="{{ old('username') }}" required autofocus
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-brand focus:border-brand outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.password') }}</label>
            <input name="password" type="password" required
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-brand focus:border-brand outline-none">
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand focus:ring-brand">
            {{ __('app.remember_me') }}
        </label>
        <button class="w-full bg-brand hover:bg-brand-dark text-white font-medium rounded-lg py-2.5 transition">
            {{ __('app.sign_in') }}
        </button>
    </form>

    <p class="text-center text-xs text-gray-400 mt-6">{{ __('app.employees_use_windows') }}</p>
@endsection
