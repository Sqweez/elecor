<?php

namespace App\Http\Resources;

use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ConnectionResource extends JsonResource
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
            'client_id' => $this->client_id,
            'user_id' => $this->user_id,
            'service_id' => $this->service_id,
            'trademark' => $this->trademark,
            'personal_account' => $this->personal_account,
            'address' => $this->address,
            'price' => $this->price,
            'is_active' => intval($this->is_active),
            'date_start' => $this->date_start,
            'month_price' => $this->month_price,
            'service_name' => $this->service->name,
            'balance' => $this->transactions->sum('balance_change'),
            'payments' => $this->payments->where('sale_id', false)->where('created_at', '>=', Carbon::today()->startOfMonth()),
            'company' => $this->company,
            'company_id' => $this->company_id,
        ];
    }
}
