<?php

namespace App\Http\Resources;

use App\Transaction;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
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
            'sum' => $this->transactions->where('balance_change', '>', 0)->sum('balance_change'),
            'personal_account' => $this->personal_account,
            'payments' => PaymentResource::collection($this->payments),
            'transaction' => TransactionResource::collection($this->transactions),
        ];
    }
}
