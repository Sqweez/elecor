<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $sale = !is_null($this->sales) ? $this->sales['name'] : null;
        return [
            'id' => $this->id,
            'price' => $this->price,
            'connection_id' => $this->connection_id,
            'sale' => $sale,
            'created_at' => $this->created_at,
        ];
    }
}
