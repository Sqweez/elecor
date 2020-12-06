<?php

namespace App\Http\Resources;

use App\BonusTransaction;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BalanceTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @mixin BonusTransaction
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'user' => $this->user->name,
            'operation' => BonusTransaction::OPERATION_TYPES[$this->operation_type]['name'],
            'comment' => $this->comment,
            'connection' => $this->operation_type === BonusTransaction::OPERATION_TYPE_DEBIT ? [
                'trademark' => $this->connection->trademark,
                'personal_account' => $this->connection->personal_account,
                'address' => $this->connection->address,
                'service' => $this->connection->service->name
            ] : null,
            'date' => Carbon::parse($this->created_at)->format('d.m.Y')
        ];
    }
}
