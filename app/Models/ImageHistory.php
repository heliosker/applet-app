<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\ImageHistory
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property string $input object
 * @property string $data object
 * @property int $created 创建时间
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 */
class ImageHistory extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'input',
        'data',
        'created',
    ];

    /**
     * @param array $input
     * @param array $complete
     * @param int $userId
     * @return ImageHistory|false
     */
    static function write(array $input, array $complete, int $userId): bool|ImageHistory
    {
        if ($complete) {
            $ch = new self();
            $ch->input = json_encode($input);
            $ch->data = json_encode($complete['data']);
            $ch->user_id = $userId;
            $ch->created = $complete['created'];
            return $ch->save() ? $ch : false;
        }
        return false;
    }
}
