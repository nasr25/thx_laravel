<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AppreciationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppreciationController extends Controller
{
    public function __construct(protected AppreciationService $appreciationService) {}

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'receiver_id' => ['required', 'integer', 'exists:users,id'],
            'reason_id'   => ['required', 'integer', 'exists:appreciation_reasons,id'],
            'message'     => ['nullable', 'string', 'max:1000'],
            'is_public'   => ['sometimes', 'boolean'],
        ], [
            'reason_id.required' => __('messages.reason_required'),
            'reason_id.exists'   => __('messages.reason_not_found'),
        ]);

        try {
            $this->appreciationService->send(
                $request->user(),
                (int) $data['receiver_id'],
                (int) $data['reason_id'],
                $data['message'] ?? null,
                $request->boolean('is_public'),
            );

            return back()->with('status', __('messages.appreciation_sent'));
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['appreciation' => $e->getMessage()])
                ->withInput();
        }
    }

    public function history(Request $request): View
    {
        $user = $request->user();

        $received = $user->receivedAppreciations()
            ->with(['sender.department', 'reason'])
            ->latest()
            ->paginate(10, ['*'], 'received_page');

        $sent = $user->sentAppreciations()
            ->with(['receiver.department', 'reason'])
            ->latest()
            ->paginate(10, ['*'], 'sent_page');

        return view('history', compact('received', 'sent'));
    }
}
