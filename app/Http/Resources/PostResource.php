<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "user" => $this->user[0]['username'],
            "post" => $this->post,
            "description" => $this->description,
            "updated_at" => $this->updated_at,
            "created_at" => $this->created_at,
        ];
    }
}
