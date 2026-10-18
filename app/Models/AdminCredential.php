<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class AdminCredential extends Model
{
    protected $fillable = ['totp_secret', 'totp_confirmed_at', 'last_login_at'];

    protected function casts(): array
    {
        return [
            'totp_confirmed_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * @return Attribute<?string, ?string>
     */
    protected function totpSecret(): Attribute
    {
        return Attribute::make(
            get: function (?string $value): ?string {
                if ($value === null || $value === '') {
                    return null;
                }

                try {
                    return decrypt($value);
                } catch (\Throwable) {
                    return $value;
                }
            },
            set: function (?string $value): ?string {
                if ($value === null || $value === '') {
                    return null;
                }

                return encrypt($value);
            },
        );
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }
}
