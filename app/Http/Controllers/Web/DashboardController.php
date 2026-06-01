<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AppreciationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected AppreciationService $appreciationService) {}

    public function index(Request $request): View
    {
        $user  = $request->user();
        $stats = $this->appreciationService->getDashboardStats($user);

        return view('dashboard', [
            'stats'       => $stats['stats'],
            'latest'      => $stats['latest_appreciations'],
            'leaderboard' => $user->hasAnyRole(['admin', 'super-admin']) ? $stats['leaderboard'] : null,
        ]);
    }
}
