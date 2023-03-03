<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 *
 * @package App\Models\User
 *
 * @property string name 名称
 * @property string email 邮件
 * @property string block_status 状态
 */
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nick_name',
        'email',
        'is_vip',
        'openid',
        'session_key',
        'avatar_url',
        'gender',
        'language',
        'city',
        'province',
        'country',
        'usable_num',
        'block_status',
        'expired_at',
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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Checking usable
     *
     * @return bool
     */
    public function checkUsable(): bool
    {
        $expiredAt = Carbon::parse($this->expired_at);
        if ($this->is_vip && $expiredAt->gt(Carbon::now())) {
            return true;
        }
        if ($this->usable_num > 0) {
            return true;
        }
        return false;
    }
}
