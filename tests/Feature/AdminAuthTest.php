<?php

use App\Models\AdminCredential;
use App\Services\TotpService;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;

beforeEach(function () {
    $this->withoutMiddleware(ValidateCsrfToken::class);
});

it('blocks the admin area without an authenticated session', function () {
    $this->get('/admin/projects')->assertRedirect(route('admin.login'));
});

it('redirects /admin and /admin/login to /login', function () {
    $this->get('/admin')->assertRedirect('/login');
    $this->get('/admin/login')->assertRedirect('/login');
});

it('rejects an invalid admin password', function () {
    Config::set('admin.password', 'secret123');

    $this->post('/login', ['password' => 'wrong'])
        ->assertSessionHasErrors('password');
});

it('logs in with the correct password and starts 2FA setup when not configured', function () {
    Config::set('admin.password', 'secret123');

    $this->post('/login', ['password' => 'secret123'])
        ->assertRedirect(route('admin.setup'));
});

it('redirects to the 2FA step when TOTP is already configured', function () {
    $secret = app(TotpService::class)->generateSecret();
    AdminCredential::create([
        'totp_secret' => $secret,
        'totp_confirmed_at' => now(),
    ]);
    Config::set('admin.password', 'secret123');

    $this->post('/login', ['password' => 'secret123'])
        ->assertRedirect(route('admin.2fa'));
});

it('completes the full login flow with a valid TOTP code', function () {
    $secret = app(TotpService::class)->generateSecret();
    AdminCredential::create([
        'totp_secret' => $secret,
        'totp_confirmed_at' => now(),
    ]);
    Config::set('admin.password', 'secret123');

    $code = (new Google2FA)->getCurrentOtp($secret);

    $this->post('/login', ['password' => 'secret123'])
        ->assertRedirect(route('admin.2fa'));

    $this->post('/login/2fa', ['code' => $code])
        ->assertRedirect(route('admin.projects.index'));

    $this->get('/admin/projects')->assertOk();
});

it('rejects an invalid TOTP code', function () {
    $secret = app(TotpService::class)->generateSecret();
    AdminCredential::create([
        'totp_secret' => $secret,
        'totp_confirmed_at' => now(),
    ]);
    Config::set('admin.password', 'secret123');

    $this->post('/login', ['password' => 'secret123']);
    $this->post('/login/2fa', ['code' => '000000'])
        ->assertSessionHasErrors('code');
});

it('logs in using a hashed admin password', function () {
    Config::set('admin.password', null);
    Config::set('admin.password_hash', Hash::make('hashed-secret-456'));

    $this->post('/login', ['password' => 'hashed-secret-456'])
        ->assertRedirect(route('admin.setup'));
});

it('clears session on logout', function () {
    $this->withSession([
        'admin_authenticated' => true,
        'admin_2fa_verified' => true,
    ]);

    $this->post(route('admin.logout'))
        ->assertRedirect(route('projects'))
        ->assertSessionMissing('admin_authenticated')
        ->assertSessionMissing('admin_2fa_verified');
});

it('supports legacy plaintext TOTP secrets from production database without error', function () {
    $secret = app(TotpService::class)->generateSecret();

    DB::table('admin_credentials')->insert([
        'totp_secret' => $secret,
        'totp_confirmed_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Config::set('admin.password', 'secret123');

    $code = (new Google2FA)->getCurrentOtp($secret);

    $this->post('/login', ['password' => 'secret123']);
    $this->post('/login/2fa', ['code' => $code])
        ->assertRedirect(route('admin.projects.index'));
});

it('resets 2FA credentials and redirects to setup when admin is password authenticated', function () {
    $secret = app(TotpService::class)->generateSecret();
    $cred = AdminCredential::create([
        'totp_secret' => $secret,
        'totp_confirmed_at' => now(),
    ]);

    $this->withSession(['admin_authenticated' => true])
        ->post(route('admin.2fa.reset'))
        ->assertRedirect(route('admin.setup'));

    $cred->refresh();
    expect($cred->totp_secret)->toBeNull()
        ->and($cred->totp_confirmed_at)->toBeNull();
});

it('blocks 2FA reset when not password authenticated', function () {
    $this->post(route('admin.2fa.reset'))
        ->assertRedirect(route('admin.login'));
});
