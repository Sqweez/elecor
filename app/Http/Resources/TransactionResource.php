<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $sale = !is_null($this->sales) ? $this->sales['name'] : null;
        return [
            'id' => $this->id,
            'balance' => $this->balance_change,
            'user_id' => intval($this->user_id),
            'sale' => $sale,
            'created_at' => $this->created_at,
        ];
    }
}
