<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function show(): View
    {
        return view('auth.login');
    }

    /**
     * Admin-only username + password form login. Employees never use this form —
     * they are authenticated automatically by IIS Windows Authentication.
     */
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

        // The form is for administrators only.
        if (! $request->user()->hasAnyRole(['admin', 'super-admin'])) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['username' => __('app.admin_only')])
                ->onlyInput('username');
        }

        $request->session()->regenerate();
        $request->user()->update(['last_login_at' => now()]);

        return redirect()->intended(route('admin.settings.edit'));
    }

    /** IIS Windows Authentication entry — logs the AD user into the session. */
    public function windows(Request $request): RedirectResponse
    {
        $identity = $this->resolveWindowsIdentity($request);
        $username = $identity !== null ? $this->normalizeWindowsUsername($identity) : null;

        if ($username === null) {
            // resolveWindowsIdentity() has already logged the empty server variables.
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
            Log::error('Windows auto-login failed on /auth/windows', [
                'identity' => $identity,
                'username' => $username,
                'error'    => $e->getMessage(),
            ]);

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
        // Identity is provided EXCLUSIVELY by IIS Windows Authentication.
        // No machine/environment fallback — that returns the server / app-pool
        // account name, never the browsing user.
        $user = $_SERVER['LOGON_USER']
            ?? $_SERVER['AUTH_USER']
            ?? $_SERVER['REMOTE_USER']
            ?? null;

        $user = is_string($user) ? trim($user) : '';

        if ($user === '') {
            Log::warning('Windows identity not provided by IIS on /auth/windows — '
                . 'check that Windows Auth is enabled and Anonymous disabled for this path.', [
                'LOGON_USER'  => $_SERVER['LOGON_USER']  ?? null,
                'AUTH_USER'   => $_SERVER['AUTH_USER']   ?? null,
                'REMOTE_USER' => $_SERVER['REMOTE_USER'] ?? null,
                'AUTH_TYPE'   => $_SERVER['AUTH_TYPE']   ?? null,
                'ip'          => $request->ip(),
            ]);

            return null;
        }

        return $user;
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
