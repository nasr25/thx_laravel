@extends('layouts.app')
@section('title', __('app.profile'))

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Profile + received --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-brand/10 text-brand flex items-center justify-center font-bold text-2xl">
                    {{ mb_strtoupper(mb_substr($user->display_name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $user->display_name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->job_title }}</p>
                    <p class="text-xs text-gray-400">{{ $user->department?->display_name }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">{{ __('app.received') }}</h3>
                @forelse ($received as $a)
                    <div class="flex items-start gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                        <div class="w-9 h-9 rounded-full bg-brand/10 text-brand flex items-center justify-center font-semibold shrink-0">
                            {{ mb_strtoupper(mb_substr($a->sender->display_name ?? '?', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-semibold text-gray-900">{{ $a->sender->display_name ?? '—' }}</span>
                                @if ($a->reason)
                                    <span class="text-[11px] font-medium text-brand bg-brand/10 px-2 py-0.5 rounded-full">{{ $a->reason->display_name }}</span>
                                @endif
                            </div>
                            @if ($a->message)
                                <p class="text-sm text-gray-600 mt-0.5">"{{ $a->message }}"</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">{{ $a->created_at?->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 py-6 text-center">{{ __('app.no_appreciations') }}</p>
                @endforelse
                <div class="mt-4">{{ $received->links() }}</div>
            </div>
        </div>

        {{-- Send appreciation --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 h-fit lg:sticky lg:top-6">
            <h3 class="font-semibold text-gray-900 mb-4">{{ __('app.send_appreciation') }}</h3>

            @if (! $canAppreciate)
                <p class="text-sm text-gray-400">{{ __('app.no_appreciations') }}</p>
            @elseif ($reasons->isEmpty())
                <p class="text-sm text-gray-400">{{ __('app.no_reasons') }}</p>
            @else
                <form method="POST" action="{{ route('appreciations.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $user->id }}">

                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">{{ __('app.reason') }} <span class="text-red-500">*</span></p>
                        <div class="space-y-2">
                            @foreach ($reasons as $reason)
                                <label class="flex items-center gap-3 p-2.5 rounded-xl border cursor-pointer
                                              {{ old('reason_id') == $reason->id ? 'border-brand bg-brand/5' : 'border-gray-200 hover:bg-gray-50' }}">
                                    <input type="radio" name="reason_id" value="{{ $reason->id }}" required
                                           class="text-brand focus:ring-brand" {{ old('reason_id') == $reason->id ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-700">{{ $reason->display_name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.message_optional') }}</label>
                        <textarea name="message" rows="3" maxlength="1000"
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-brand focus:border-brand outline-none"
                                  placeholder="{{ __('app.message_placeholder') }}">{{ old('message') }}</textarea>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="is_public" value="1" checked class="rounded border-gray-300 text-brand focus:ring-brand">
                        {{ __('app.make_public') }}
                    </label>

                    <button class="w-full bg-brand hover:bg-brand-dark text-white font-medium rounded-lg py-2.5 transition">
                        {{ __('app.send') }} ⭐
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection
