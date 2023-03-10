<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\ChatHistory
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory query()
 * @mixin \Eloquent
 * @mixin IdeHelperChatHistory
 */
class ChatHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'human',
        'ai',
        'chat_id',
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
     * @return ChatHistory|bool
     */
    static function write(string $complete, string $prompt, int $userId): ChatHistory|bool
    {
        if ($chat = json_decode($complete, true)) {
            $ch = new self();
            $ch->human = $prompt;
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
