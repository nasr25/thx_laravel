<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AppreciationReason;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::active()
            ->with('department')
            ->where('id', '!=', $request->user()->id);

        if ($term = trim((string) $request->get('q'))) {
            $query->search($term);
        }

        $employees = $query->orderBy('full_name')->paginate(15)->withQueryString();

        return view('employees.index', compact('employees'));
    }

    public function show(Request $request, User $user): View
    {
        $user->load('department');

        $received = $user->receivedAppreciations()
            ->with(['sender.department', 'reason'])
            ->latest()
            ->paginate(10);

        $reasons = AppreciationReason::active()
            ->orderBy('sort_order')->orderBy('name')->get();

        $canAppreciate = $request->user()->id !== $user->id;

        return view('employees.show', compact('user', 'received', 'reasons', 'canAppreciate'));
    }
}
