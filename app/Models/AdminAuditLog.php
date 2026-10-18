<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class AdminAuditLog extends Model
{
    use Prunable;

    public const EVENT_PASSWORD_OK = 'password_ok';

    public const EVENT_PASSWORD_FAILED = 'password_failed';

    public const EVENT_TOTP_OK = 'totp_ok';

    public const EVENT_TOTP_FAILED = 'totp_failed';

    public const EVENT_LOGIN_SUCCESS = 'login_success';

    public const EVENT_LOGOUT = 'logout';

    public const EVENT_TOTP_RESET = 'totp_reset';

    protected $fillable = ['event', 'ip', 'user_agent'];

    public static function record(string $event, ?string $ip = null, ?string $userAgent = null): void
    {
        static::create([
            'event' => $event,
            'ip' => $ip,
            'user_agent' => $userAgent ? mb_substr($userAgent, 0, 255) : null,
        ]);
    }

    public function label(): string
    {
        return match ($this->event) {
            self::EVENT_PASSWORD_OK => 'Password accepted',
            self::EVENT_PASSWORD_FAILED => 'Wrong password',
            self::EVENT_TOTP_OK => '2FA code accepted',
            self::EVENT_TOTP_FAILED => 'Wrong 2FA code',
            self::EVENT_LOGIN_SUCCESS => 'Login successful',
            self::EVENT_LOGOUT => 'Logged out',
            self::EVENT_TOTP_RESET => '2FA reset',
            default => $this->event,
        };
    }

    public function isFailure(): bool
    {
        return in_array($this->event, [
            self::EVENT_PASSWORD_FAILED,
            self::EVENT_TOTP_FAILED,
        ], true);
    }

    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(90));
    }
}
