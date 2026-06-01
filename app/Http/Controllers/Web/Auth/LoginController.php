<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function show(): View
    {
        return view('auth.login');
    }

    /** Username + password form login (admins and, in dev, seeded users). */
    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $ok = Auth::attempt(
            ['username' => $data['username'], 'password' => $data['password'], 'is_active' => true],
            $request->boolean('remember')
        );

        if (! $ok) {
            return back()
                ->withErrors(['username' => __('auth.failed')])
                ->onlyInput('username');
        }

        $request->session()->regenerate();
        $request->user()->update(['last_login_at' => now()]);

        return redirect()->intended(route('dashboard'));
    }

    /** IIS Windows Authentication entry — logs the AD user into the session. */
    public function windows(Request $request): RedirectResponse
    {
        $identity = $this->resolveWindowsIdentity($request);
        $username = $identity !== null ? $this->normalizeWindowsUsername($identity) : null;

        if ($username === null) {
            return redirect()->route('login')
                ->withErrors(['username' => __('app.windows_not_detected')]);
        }

        try {
            $user = $this->authService->resolveWindowsUser($username, $identity);
            abort_unless($user->is_active, 403, __('messages.account_inactive'));

            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors(['username' => $e->getMessage()]);
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ─── IIS Windows identity helpers (server variables only) ────────────────

    private function resolveWindowsIdentity(Request $request): ?string
    {
        $candidates = [
            $_SERVER['LOGON_USER']  ?? null,
            $_SERVER['AUTH_USER']   ?? null,
            $_SERVER['REMOTE_USER'] ?? null,
            $request->server('LOGON_USER'),
            $request->server('AUTH_USER'),
            $request->server('REMOTE_USER'),
            $request->header('X-Windows-User'),
        ];

        foreach ($candidates as $value) {
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        if (app()->isLocal()) {
            $username = getenv('USERNAME') ?: null;
            $domain   = getenv('USERDOMAIN') ?: getenv('COMPUTERNAME') ?: null;
            if ($username && !in_array(strtoupper((string) $domain), ['BUILTIN', 'NT AUTHORITY', ''], true)) {
                return $domain ? "{$domain}\\{$username}" : $username;
            }
        }

        return null;
    }

    private function normalizeWindowsUsername(string $identity): ?string
    {
        $identity = trim($identity);

        if (str_contains($identity, '\\')) {
            $identity = substr($identity, strrpos($identity, '\\') + 1);
        } elseif (str_contains($identity, '@')) {
            $identity = strstr($identity, '@', true);
        }

        $identity = strtolower(trim($identity));

        return $identity !== '' ? $identity : null;
    }
}
