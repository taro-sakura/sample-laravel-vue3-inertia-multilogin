<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use PragmaRX\Google2FA\Google2FA;

class Admin extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * 新しい二要素認証のシークレットを生成
     */
    public function generateNewSecret(): string
    {
        $google2fa = new Google2FA();
        return $google2fa->generateSecretKey();
    }

    /**
     * 新しいリカバリーコードを生成
     */
    public function generateNewRecoveryCodes(): array
    {
        return collect(range(1, 8))->map(function () {
            return sprintf('%s-%s-%s', 
                bin2hex(random_bytes(4)),
                bin2hex(random_bytes(4)),
                bin2hex(random_bytes(4))
            );
        })->toArray();
    }

    protected $appends = [
        'profile_photo_url',
    ];
} 