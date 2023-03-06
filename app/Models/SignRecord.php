<?php

namespace App\Models;

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

}
