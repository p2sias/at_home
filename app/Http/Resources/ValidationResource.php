<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ValidationResource extends JsonResource
{

    public function boot()
    {
        ValidationResource::withoutWrapping();
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'status' => $this->status,
            'user' => $this->user,
            'challenge' => $this->challenge,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'session_id' => $this->challenge->session->id,
            'files' => FileResource::collection($this->files)
        ];
    }
}
