<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            // 'user_id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'formatted_name' => $this->formattedName(),
            'email' => $this->email,
            /// 'mobile' => $this->mobile,
        ];
    }
}
