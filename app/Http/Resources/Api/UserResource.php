<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        switch ($this->block_status) {
            case 0:
                $this->block_status_label = '正常';
                break;
            case 1:
                $this->block_status_label = '冻结';
                break;
        }
        switch ($this->gender) {
            case -1:
                $this->gender_label = '未定义';
                break;
            case 0:
                $this->gender_label = '男';
                break;
            case 1:
                $this->gender_label = '女';
                break;
        }
        return [
            'id' => $this->id,
            'nick_name' => $this->nick_name,
            'avatar_url' => $this->avatar_url,
            'is_vip' => $this->is_vip,
            'usable_num' => $this->usable_num,
            'block_status' => [
                'key' => $this->block_status,
                'label' => $this->block_status_label
            ],
            'gender' => [
                'key' => $this->gender,
                'label' => $this->gender_label,
            ],
            'language' => $this->language ?? '',
            'city' => $this->city ?? '',
            'province' => $this->province ?? '',
            'country' => $this->country ?? '',
            'expired_at' => (string)$this->expired_at,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at
        ];
    }
}
