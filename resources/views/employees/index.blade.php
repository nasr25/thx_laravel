@extends('layouts.app')
@section('title', __('app.find_employees'))

@section('content')
    <form method="GET" action="{{ route('employees.index') }}" class="mb-6 max-w-md">
        <input name="q" value="{{ request('q') }}" placeholder="{{ __('app.search_placeholder') }}"
               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-brand focus:border-brand outline-none">
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($employees as $employee)
            <div class="bg-white rounded-2xl border border-gray-100 p-5 flex flex-col">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-full bg-brand/10 text-brand flex items-center justify-center font-bold text-lg">
                        {{ mb_strtoupper(mb_substr($employee->display_name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 truncate">{{ $employee->display_name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $employee->job_title }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mb-4">{{ $employee->department?->display_name }}</p>
                <a href="{{ route('employees.show', $employee) }}"
                   class="mt-auto text-center text-sm font-medium text-white bg-brand hover:bg-brand-dark rounded-lg py-2 transition">
                    {{ __('app.view_profile') }}
                </a>
            </div>
        @empty
            <p class="text-gray-400 col-span-full text-center py-10">{{ __('app.no_results') }}</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $employees->links() }}</div>
@endsection
