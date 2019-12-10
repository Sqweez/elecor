<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MobileStocksResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {

        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'image' => url('/') . Storage::disk('local')->url($this->image),
            'service_id' => $this->service->id ?? null
        ];
    }
}
