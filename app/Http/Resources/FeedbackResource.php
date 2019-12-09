<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
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
            'client' => $this->client->name ?? 'Гость',
            'client_id' => $this->client_id,
            'feedback' => $this->feedback,
            'answer' => $this->answer,
            'date' => Carbon::parse($this->created_at)->format('d.m.Y'),
            'is_worked' => $this->is_worked,
        ];
    }
}
