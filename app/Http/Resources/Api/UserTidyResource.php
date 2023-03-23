<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTidyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'open_id' => $this->openid,
            'usable_num' => $this->usable_num,
            'is_vip' => $this->is_vip,
            'expired_at' => (string)$this->expired_at,
        ];
    }
}
