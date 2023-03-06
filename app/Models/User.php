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
 * App\Models\User
 *
 * @property int $id
 * @property string $nick_name 昵称
 * @property string $email 邮箱
 * @property int $gender 性别
 * @property int $is_vip 是否会员
 * @property string $openid OPENID
 * @property string $session_key SESSION KEY
 * @property string $avatar_url 头像
 * @property int $usable_num 剩余次数
 * @property int $block_status 用户状态 0代表正常 1代表冻结
 * @property string|null $city 城市
 * @property string|null $province 省份
 * @property string|null $country 国家
 * @property string|null $language 语言
 * @property string $password 密码
 * @property \Illuminate\Support\Carbon|null $email_verified_at 邮箱验证时间
 * @property string|null $expired_at 会员到期时间
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 最后一次更新时间
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBlockStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsVip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNickName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSessionKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsableNum($value)
 * @mixin \Eloquent
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
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * @param int $incrNum
     * @return bool
     */
    public function incrUsableNum(int $incrNum = 1): bool
    {
        $this->usable_num += $incrNum;
        return $this->save();
    }


    /**
     * @param int $decrNum
     * @return bool
     */
    public function decrUsableNum(int $decrNum = 1): bool
    {
        $this->usable_num -= $decrNum;
        return $this->save();
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
