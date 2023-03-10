<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ShareHistories
 *
 * @property int $id
 * @property int $invitee_id 被邀请人
 * @property int $inviter_id 邀请人
 * @property \Illuminate\Support\Carbon|null $created_at 邀请时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|ShareHistories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShareHistories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShareHistories query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShareHistories whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareHistories whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareHistories whereInviteeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareHistories whereInviterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareHistories whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ShareHistories extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitee_id',
        'inviter_id',
    ];
}
