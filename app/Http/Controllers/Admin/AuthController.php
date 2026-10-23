<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\AdminCredential;
use App\Services\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private readonly TotpService $totp) {}

    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('admin_2fa_verified')) {
            return redirect()->route('admin.projects.index');
        }

        if ($request->session()->get('admin_authenticated')) {
            return redirect()->route('admin.2fa');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $inputPassword = (string) $request->input('password');
        $passwordHash = config('admin.password_hash');
        $plainPassword = (string) config('admin.password');

        $isValid = false;
        if (! empty($passwordHash)) {
            $isValid = Hash::check($inputPassword, (string) $passwordHash);
        } elseif ($plainPassword !== '') {
            if (str_starts_with($plainPassword, '$2y$') || str_starts_with($plainPassword, '$argon2')) {
                $isValid = Hash::check($inputPassword, $plainPassword);
            } else {
                $isValid = hash_equals($plainPassword, $inputPassword);
            }
        }

        if (! $isValid) {
            AdminAuditLog::record(AdminAuditLog::EVENT_PASSWORD_FAILED, $request->ip(), $request->userAgent());

            return back()->withErrors(['password' => 'Invalid credentials.']);
        }

        AdminAuditLog::record(AdminAuditLog::EVENT_PASSWORD_OK, $request->ip(), $request->userAgent());

        $request->session()->regenerate();
        $request->session()->put('admin_authenticated', true);
        $request->session()->forget('admin_2fa_verified');

        $credential = AdminCredential::current();

        if (! $credential->totp_secret || ! $credential->totp_confirmed_at) {
            return redirect()->route('admin.setup');
        }

        return redirect()->route('admin.2fa');
    }

    public function showSetup(Request $request): View|RedirectResponse
    {
        if (! $request->session()->get('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        $credential = AdminCredential::current();

        if ($credential->totp_secret && $credential->totp_confirmed_at) {
            return redirect()->route('admin.2fa');
        }

        $secret = $request->session()->get('admin_pending_secret');

        if (! $secret) {
            $secret = $this->totp->generateSecret();
            $request->session()->put('admin_pending_secret', $secret);
        }

        $otpauthUrl = $this->totp->otpauthUrl((string) config('admin.totp_holder'), $secret);

        return view('admin.setup', [
            'secret' => $secret,
            'qrSvg' => $this->totp->qrSvg($otpauthUrl),
        ]);
    }

    public function confirmSetup(Request $request): RedirectResponse
    {
        if (! $request->session()->get('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $secret = (string) $request->session()->get('admin_pending_secret');

        if ($secret === '' || ! $this->totp->verify($secret, (string) $request->input('code'))) {
            AdminAuditLog::record(AdminAuditLog::EVENT_TOTP_FAILED, $request->ip(), $request->userAgent());

            return back()->withErrors(['code' => 'Invalid code, try again.']);
        }

        $credential = AdminCredential::current();
        $credential->forceFill([
            'totp_secret' => $secret,
            'totp_confirmed_at' => now(),
            'last_login_at' => now(),
        ])->save();

        $request->session()->forget('admin_pending_secret');
        $request->session()->regenerate();
        $request->session()->put('admin_2fa_verified', true);

        AdminAuditLog::record(AdminAuditLog::EVENT_TOTP_OK, $request->ip(), $request->userAgent());
        AdminAuditLog::record(AdminAuditLog::EVENT_LOGIN_SUCCESS, $request->ip(), $request->userAgent());

        return redirect()->route('admin.projects.index');
    }

    public function show2fa(Request $request): View|RedirectResponse
    {
        if (! $request->session()->get('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        $credential = AdminCredential::current();

        if (! $credential->totp_secret || ! $credential->totp_confirmed_at) {
            return redirect()->route('admin.setup');
        }

        if ($request->session()->get('admin_2fa_verified')) {
            return redirect()->route('admin.projects.index');
        }

        return view('admin.two-factor');
    }

    public function verify2fa(Request $request): RedirectResponse
    {
        if (! $request->session()->get('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $credential = AdminCredential::current();

        if (! $credential->totp_secret || ! $this->totp->verify($credential->totp_secret, (string) $request->input('code'))) {
            AdminAuditLog::record(AdminAuditLog::EVENT_TOTP_FAILED, $request->ip(), $request->userAgent());

            return back()->withErrors(['code' => 'Invalid code, try again.']);
        }

        $request->session()->regenerate();
        $request->session()->put('admin_2fa_verified', true);
        $credential->forceFill(['last_login_at' => now()])->save();

        AdminAuditLog::record(AdminAuditLog::EVENT_TOTP_OK, $request->ip(), $request->userAgent());
        AdminAuditLog::record(AdminAuditLog::EVENT_LOGIN_SUCCESS, $request->ip(), $request->userAgent());

        return redirect()->route('admin.projects.index');
    }

    public function reset2fa(Request $request): RedirectResponse
    {
        if (! $request->session()->get('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        $credential = AdminCredential::current();
        $credential->forceFill([
            'totp_secret' => null,
            'totp_confirmed_at' => null,
        ])->save();

        $request->session()->forget(['admin_pending_secret', 'admin_2fa_verified']);

        return redirect()->route('admin.setup');
    }

    public function logout(Request $request): RedirectResponse
    {
        AdminAuditLog::record(AdminAuditLog::EVENT_LOGOUT, $request->ip(), $request->userAgent());

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('projects');
    }
}
