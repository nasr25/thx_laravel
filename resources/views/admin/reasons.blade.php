@extends('layouts.app')
@section('title', __('app.appreciation_reasons'))

@section('content')
    <p class="text-sm text-gray-500 mb-6">{{ __('app.reason_management_hint') }}</p>

    {{-- Add reason --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
        <h3 class="font-semibold text-gray-900 mb-4">{{ __('app.add_reason') }}</h3>
        <form method="POST" action="{{ route('admin.reasons.store') }}" class="grid sm:grid-cols-12 gap-3 items-end">
            @csrf
            <div class="sm:col-span-4">
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.name_en') }} *</label>
                <input name="name" required value="{{ old('name') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            </div>
            <div class="sm:col-span-4">
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.name_ar') }}</label>
                <input name="name_ar" dir="rtl" value="{{ old('name_ar') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.sort_order') }}</label>
                <input name="sort_order" type="number" min="0" value="{{ old('sort_order', 0) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            </div>
            <div class="sm:col-span-2">
                <label class="flex items-center gap-2 text-sm text-gray-700 mb-2">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-brand focus:ring-brand"> {{ __('app.active') }}
                </label>
                <button class="w-full bg-brand hover:bg-brand-dark text-white text-sm rounded-lg py-2">{{ __('app.add_reason') }}</button>
            </div>
        </form>
    </div>

    {{-- List --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        @forelse ($reasons as $reason)
            <div class="py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <span class="font-medium text-gray-900">{{ $reason->name }}</span>
                        @if ($reason->name_ar)<span class="text-gray-400 text-sm" dir="rtl"> · {{ $reason->name_ar }}</span>@endif
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $reason->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $reason->is_active ? __('app.active') : '—' }}
                    </span>
                    <details class="relative">
                        <summary class="cursor-pointer text-sm text-gray-500 hover:text-brand list-none">{{ __('app.edit') }}</summary>
                        <form method="POST" action="{{ route('admin.reasons.update', $reason) }}" class="absolute end-0 mt-2 z-10 w-72 bg-white border border-gray-200 rounded-xl shadow-lg p-4 space-y-3">
                            @csrf
                            @method('PUT')
                            <input name="name" value="{{ $reason->name }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="{{ __('app.name_en') }}">
                            <input name="name_ar" dir="rtl" value="{{ $reason->name_ar }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="{{ __('app.name_ar') }}">
                            <input name="sort_order" type="number" min="0" value="{{ $reason->sort_order }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="{{ __('app.sort_order') }}">
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="is_active" value="1" {{ $reason->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-brand focus:ring-brand"> {{ __('app.active') }}
                            </label>
                            <button class="w-full bg-brand text-white text-sm rounded-lg py-2">{{ __('app.save_short') }}</button>
                        </form>
                    </details>
                    <form method="POST" action="{{ route('admin.reasons.destroy', $reason) }}" onsubmit="return confirm('{{ __('app.confirm_delete_reason') }}')">
                        @csrf
                        @method('DELETE')
                        <button class="text-sm text-red-500 hover:text-red-700">{{ __('app.delete') }}</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400 py-6 text-center">{{ __('app.no_reasons') }}</p>
        @endforelse
    </div>
@endsection
