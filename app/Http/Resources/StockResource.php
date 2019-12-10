<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
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
            'title' => $this->title,
            'body' => $this->body,
            'date' => Carbon::parse($this->created_at)->format('d.m.Y'),
            'is_visible' => $this->is_visible,
            'image' => $this->image,
            'service' => $this->service->name ?? null,
            'service_id' => $this->service_id
        ];
    }
}
