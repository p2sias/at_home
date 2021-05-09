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
            'id' => $this->id,
            'api_token' => $this->api_token,
            'name' => $this->name,
            'surname' => $this->surname,
            'pseudo' => $this->pseudo,
            'email' => $this->email,
            'birthday' => $this->birthday,
            'phone' => $this->phone,
            'auth_level' => $this->auth_level,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'session' => new SessionResource($this->session),
            'challenges' => ChallengeResource::collection($this->challenges),
            'validations' => $this->validations
        ];
    }
}
