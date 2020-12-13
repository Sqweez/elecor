<?php

namespace App\Http\Resources;

use App\Transaction;
use App\Client;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @mixin Client
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'push_token' => $this->push_token,
            'name' => $this->name,
            'phone' => $this->phones[0]->phone ?? '',
            'connections' => MobileConnectionResource::collection($this->connections->where('is_active', 1)->except(['created_at', 'updated_at'])),
            'transactions' => MobileTransactionResource::collection($this->transactions->where('is_visible', true)->sortByDesc('created_at')),
        ];
    }
}
