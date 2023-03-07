<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\SignRecord
 *
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SignRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SignRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SignRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|SignRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignRecord whereUserId($value)
 * @mixin \Eloquent
 */
class SignRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    /**
     * 已签到
     */
    const SIGNED_IN = true;

    /**
     * 未签到
     */
    const NOT_SIGNED_IN = false;


    /**
     * @param $uid
     * @return bool
     */
    static function isTodaySignedIn($uid): bool
    {
        if (self::where('user_id', $uid)
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->first()) {
            return true;
        }
        return false;
    }

}
