<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DebtResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $connections = DebtConnectionResource::collection($this->connections)->toArray($request);

        $connections = array_filter($connections, function ($arr) {
            return $arr !== null;
        });

        if (count($connections) === 0) {
            return null;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'connections' => $connections,
        ];
    }
}
