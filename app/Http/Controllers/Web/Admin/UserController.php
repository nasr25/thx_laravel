<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with(['department', 'roles'])->withCount('receivedAppreciations');

        if ($term = trim((string) $request->get('q'))) {
            $query->search($term);
        }

        $users = $query->orderBy('full_name')->paginate(20)->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $request->validate(['is_admin' => ['required', 'boolean']]);

        if (! $request->boolean('is_admin') && $user->id === $request->user()->id) {
            return back()->withErrors(['user' => __('messages.cannot_demote_self')]);
        }

        $user->syncRoles([$request->boolean('is_admin') ? 'admin' : 'employee']);

        ActivityLog::log(
            $request->boolean('is_admin') ? 'grant_admin' : 'revoke_admin',
            "Updated admin access for {$user->full_name}",
            ['target_user_id' => $user->id]
        );

        return back()->with('status', __('messages.role_updated'));
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $request->validate(['is_active' => ['required', 'boolean']]);

        if (! $request->boolean('is_active') && $user->id === $request->user()->id) {
            return back()->withErrors(['user' => __('messages.cannot_demote_self')]);
        }

        $user->update(['is_active' => $request->boolean('is_active')]);

        ActivityLog::log('update_user_status', "Set {$user->full_name} active=" . ($request->boolean('is_active') ? '1' : '0'));

        return back()->with('status', __('messages.role_updated'));
    }
}
