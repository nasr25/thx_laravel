<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AppreciationReason;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Full CRUD for appreciation reasons — super-admin only (gated in routes). */
class ReasonController extends Controller
{
    public function index(): View
    {
        $reasons = AppreciationReason::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.reasons', compact('reasons'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'name_ar'    => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['sometimes', 'boolean'],
        ]);

        $reason = AppreciationReason::create([
            'name'       => $data['name'],
            'name_ar'    => $data['name_ar'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        ActivityLog::log('create_reason', "Created appreciation reason '{$reason->name}'", ['reason_id' => $reason->id]);

        return back()->with('status', __('messages.reason_created'));
    }

    public function update(Request $request, AppreciationReason $reason): RedirectResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'name_ar'    => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['sometimes', 'boolean'],
        ]);

        $reason->update([
            'name'       => $data['name'],
            'name_ar'    => $data['name_ar'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active'  => $request->boolean('is_active'),
        ]);

        ActivityLog::log('update_reason', "Updated appreciation reason '{$reason->name}'", ['reason_id' => $reason->id]);

        return back()->with('status', __('messages.reason_updated'));
    }

    public function destroy(AppreciationReason $reason): RedirectResponse
    {
        $name = $reason->name;
        $reason->delete();

        ActivityLog::log('delete_reason', "Deleted appreciation reason '{$name}'", ['reason_id' => $reason->id]);

        return back()->with('status', __('messages.reason_deleted'));
    }
}
