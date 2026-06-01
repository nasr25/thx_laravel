@extends('layouts.app')
@section('title', __('app.dashboard'))

@section('content')
    <h2 class="text-xl font-bold text-gray-900 mb-6">{{ __('app.welcome', ['name' => auth()->user()->display_name]) }}</h2>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @php
            $cards = [
                [__('app.total_received'), $stats['total_received'] ?? 0],
                [__('app.this_month'), $stats['monthly_received'] ?? 0],
                [__('app.total_sent'), $stats['total_sent'] ?? 0],
                [__('app.monthly_remaining'), $stats['monthly_remaining'] ?? 0],
            ];
        @endphp
        @foreach ($cards as [$label, $value])
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-3xl font-bold text-brand">{{ $value }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-{{ $leaderboard ? '3' : '1' }} gap-6">
        {{-- Latest appreciations --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-900 mb-4">{{ __('app.latest_appreciations') }}</h3>
            @forelse ($latest as $appreciation)
                <div class="flex items-start gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                    <div class="w-9 h-9 rounded-full bg-brand/10 text-brand flex items-center justify-center font-semibold shrink-0">
                        {{ mb_strtoupper(mb_substr($appreciation->sender->display_name ?? '?', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-semibold text-gray-900">{{ $appreciation->sender->display_name ?? '—' }}</span>
                            @if ($appreciation->reason)
                                <span class="text-[11px] font-medium text-brand bg-brand/10 px-2 py-0.5 rounded-full">{{ $appreciation->reason->display_name }}</span>
                            @endif
                        </div>
                        @if ($appreciation->message)
                            <p class="text-sm text-gray-600 mt-0.5">"{{ $appreciation->message }}"</p>
                        @else
                            <p class="text-sm text-gray-400 italic mt-0.5">{{ __('app.no_message') }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">{{ $appreciation->created_at?->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 py-6 text-center">{{ __('app.no_appreciations') }}</p>
            @endforelse
        </div>

        {{-- Leaderboard (admins) --}}
        @if ($leaderboard)
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4">{{ __('app.leaderboard') }}</h3>
                <ol class="space-y-3">
                    @foreach ($leaderboard as $i => $u)
                        <li class="flex items-center gap-3 text-sm">
                            <span class="w-6 text-center font-bold text-gray-400">{{ $i + 1 }}</span>
                            <span class="flex-1 truncate text-gray-800">{{ $u->display_name }}</span>
                            <span class="font-semibold text-brand">{{ $u->appreciation_count }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif
    </div>
@endsection
