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
        switch ($this->block_status){
            case 0:
                $this->block_status_txt = '正常';
                break;
            case 1:
                $this->block_status_txt = '冻结';
                break;
        }
        return [
            'id'=>$this->id,
            'name' => $this->name,
            'avatar' => $this->avatar,
            'is_vip'=>$this->is_vip,
            'usable_num'=>$this->usable_num,
            'block_status' => [
                'key'=>$this->block_status,
                'label'=>$this->block_status_txt
            ],
            'expired_at'=>(string)$this->expired_at,
            'created_at'=>(string)$this->created_at,
            'updated_at'=>(string)$this->updated_at
        ];
    }
}
