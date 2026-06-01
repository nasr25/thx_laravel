@extends('layouts.app')
@section('title', __('app.my_history'))

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Received --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">{{ __('app.received') }}</h3>
            @forelse ($received as $a)
                <div class="py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-semibold text-gray-900">{{ __('app.from') }}: {{ $a->sender->display_name ?? '—' }}</span>
                        @if ($a->reason)
                            <span class="text-[11px] font-medium text-brand bg-brand/10 px-2 py-0.5 rounded-full">{{ $a->reason->display_name }}</span>
                        @endif
                    </div>
                    @if ($a->message)<p class="text-sm text-gray-600 mt-0.5">"{{ $a->message }}"</p>@endif
                    <p class="text-xs text-gray-400 mt-1">{{ $a->created_at?->diffForHumans() }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-400 py-6 text-center">{{ __('app.no_received') }}</p>
            @endforelse
            <div class="mt-4">{{ $received->links() }}</div>
        </div>

        {{-- Sent --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">{{ __('app.sent') }}</h3>
            @forelse ($sent as $a)
                <div class="py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-semibold text-gray-900">{{ __('app.to') }}: {{ $a->receiver->display_name ?? '—' }}</span>
                        @if ($a->reason)
                            <span class="text-[11px] font-medium text-brand bg-brand/10 px-2 py-0.5 rounded-full">{{ $a->reason->display_name }}</span>
                        @endif
                    </div>
                    @if ($a->message)<p class="text-sm text-gray-600 mt-0.5">"{{ $a->message }}"</p>@endif
                    <p class="text-xs text-gray-400 mt-1">{{ $a->created_at?->diffForHumans() }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-400 py-6 text-center">{{ __('app.no_sent') }}</p>
            @endforelse
            <div class="mt-4">{{ $sent->links() }}</div>
        </div>
    </div>
@endsection
