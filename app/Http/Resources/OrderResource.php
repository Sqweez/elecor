<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'client' => $this->client->name,
            'client_id' => $this->client_id,
            'feedback' => $this->feedback,
            'service' => $this->service->name,
            'answer' => $this->answer,
            'date' => Carbon::parse($this->created_at)->format('d.m.Y'),
            'is_worked' => $this->is_worked,
            'comment' => $this->comment,
            'phone' => $this->phone,
            'client_comment' => $this->client_comment
        ];
    }
}
