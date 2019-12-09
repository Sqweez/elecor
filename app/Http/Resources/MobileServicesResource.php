<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MobileServicesResource extends JsonResource
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
            'title' => $this->name,
            'description' => $this->description,
            'icon' => url('/') . Storage::disk('local')->url($this->icon),
            'image' => url('/') . Storage::disk('local')->url($this->image),
            'main_id' => $this->main_id,
            'additional_information' => $this->additional_information,
        ];
    }
}
