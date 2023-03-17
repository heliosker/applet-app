<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UsageRecords
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $variable 改变数值
 * @property int $original 原始的
 * @property int $usable 可用的
 * @property string $reason 变动的原因
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UsageRecords newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UsageRecords newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UsageRecords query()
 * @method static \Illuminate\Database\Eloquent\Builder|UsageRecords whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UsageRecords whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UsageRecords whereOriginal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UsageRecords whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UsageRecords whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UsageRecords whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UsageRecords whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UsageRecords whereVariable($value)
 * @mixin \Eloquent
 */
class UsageRecords extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original',
        'variable',
        'usable',
        'reason',
    ];
}
