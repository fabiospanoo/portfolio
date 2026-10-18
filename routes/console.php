<?php

use App\Models\AdminCredential;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:reset-2fa', function () {
    AdminCredential::query()->delete();
    $this->info('Admin 2FA credentials have been reset. You can now log in with your password and scan a new QR code.');
})->purpose('Reset admin two-factor authentication credentials');
