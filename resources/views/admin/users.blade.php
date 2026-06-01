@extends('layouts.app')
@section('title', __('app.user_management'))

@section('content')
    <p class="text-sm text-gray-500 mb-6">{{ __('app.user_management_hint') }}</p>

    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 max-w-md">
        <input name="q" value="{{ request('q') }}" placeholder="{{ __('app.search') }}…"
               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-brand focus:border-brand outline-none">
    </form>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="text-start font-medium px-4 py-3">{{ __('app.user') }}</th>
                    <th class="text-start font-medium px-4 py-3 hidden sm:table-cell">{{ __('app.department') }}</th>
                    <th class="text-center font-medium px-4 py-3">{{ __('app.is_admin') }}</th>
                    <th class="text-center font-medium px-4 py-3">{{ __('app.is_active') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach ($users as $u)
                    @php
                        $uIsAdmin = $u->roles->contains(fn ($r) => in_array($r->name, ['admin', 'super-admin']));
                        $isSelf   = $u->id === auth()->id();
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $u->full_name }}</p>
                            <p class="text-xs text-gray-400">{{ '@' . $u->username }}</p>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell text-gray-500">{{ $u->department?->display_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('admin.users.role', $u) }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="is_admin" value="{{ $uIsAdmin ? 0 : 1 }}">
                                <button {{ $isSelf ? 'disabled' : '' }}
                                        class="text-xs rounded-lg px-3 py-1.5 {{ $uIsAdmin ? 'bg-brand/10 text-brand' : 'bg-gray-100 text-gray-600' }} {{ $isSelf ? 'opacity-40 cursor-not-allowed' : 'hover:opacity-80' }}">
                                    {{ $uIsAdmin ? '✓ ' . __('app.is_admin') : '—' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('admin.users.status', $u) }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="is_active" value="{{ $u->is_active ? 0 : 1 }}">
                                <button {{ $isSelf ? 'disabled' : '' }}
                                        class="text-xs rounded-lg px-3 py-1.5 {{ $u->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }} {{ $isSelf ? 'opacity-40 cursor-not-allowed' : 'hover:opacity-80' }}">
                                    {{ $u->is_active ? '✓ ' . __('app.is_active') : '—' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
@endsection
