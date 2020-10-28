<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleClientResource extends JsonResource
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
            'push_token' => $this->push_token,
            'name' => $this->name,
            'comment' => $this->comment,
            'client_type' => intval($this->client_type),
            'photo' => $this->photo,
            'type' => new ClientTypeResource($this->type),
            'phones' => PhoneResource::collection($this->phones),
            'connections' => ConnectionResource::collection($this->connections->where('is_deleted', false)->except(['created_at', 'updated_at'])),
            'additional_fields' => json_decode($this->additional_fields, true),
            'gender' => $this->gender,
            'lang' => $this->lang,
            'birth_date' => $this->birth_date ?? null,
        ];
    }
}
