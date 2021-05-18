<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LogsResource extends JsonResource
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
            'id' => $this->id,
            'message' => $this->message,
            'author' => $this->user->pseudo,
            'created_at' => date('d/m/Y', strtotime($this->created_at)),
            'created_at_hour' => date('H:i', strtotime($this->created_at))
        ];
    }
}
