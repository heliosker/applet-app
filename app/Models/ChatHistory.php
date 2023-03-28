<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


/**
 * App\Models\ChatHistory
 *
 * @property int $id
 * @property string $chat_id 消息ID
 * @property int $user_id 用户ID
 * @property string $object object
 * @property string $model 模型
 * @property string $human 人类问题
 * @property string $ai AI 回答
 * @property string $scene 场景
 * @property int $created 创建时间
 * @property mixed|null $usage 使用量
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereAi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereHuman($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereObject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereUsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereUserId($value)
 * @mixin \Eloquent
 */
class ChatHistory extends Model
{
    use HasFactory;

    const SCENE_CHAT = 1;
    const SCENE_BABY_NAME = 2;


    protected $fillable = [
        'user_id',
        'human',
        'ai',
        'chat_id',
        'scene',
        'object',
        'model',
        'usage',
        'created',
    ];

    /**
     * @param $chatId
     * @return array|null
     */
    static function lastMessages($chatId): ?array
    {
//        $history[] = ['role' => 'system', "content" => "You are a helpful assistant."];
//        $history[] = ['role' => 'user', "content" => $input['prompt']];
//        $history[] = ['role' => 'assistant', "content" => ''];
        if ($last = self::where('chat_id', $chatId)->first()) {
            $messages[] = [
                "role" => "user",
                "content" => $last->human,
            ];
            $messages[] = [
                "role" => "assistant",
                "content" => $last->ai,
            ];
            return $messages;
        }

        return null;
    }

    /**
     * @param string $complete
     * @param string $prompt
     * @param int $userId
     * @param int $scene
     * @return ChatHistory|bool
     */
    static function write(string $complete, string $prompt, int $userId, int $scene = self::SCENE_CHAT): ChatHistory|bool
    {
        $chat = json_decode($complete, true);
        if ($chat && is_array($chat)) {
            $ch = new self();
            $ch->human = $prompt;
            $ch->scene = $scene;
            $ch->chat_id = $chat['id'];
            $ch->object = $chat['object'];
            $ch->created = $chat['created'];
            $ch->model = $chat['model'];
            $ch->usage = json_encode($chat['usage']);
            $ch->ai = $chat['choices'][0]['message']['content'];
            $ch->user_id = $userId;
            return $ch->save() ? $ch : false;
        }
        return false;
    }
}
