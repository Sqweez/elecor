<?php

namespace App\Http\Resources;

use App\Connection;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $connection = Connection::find($this->connection_id)->only(['personal_account', 'service_id', 'is_active']);

        if ($connection['is_active'] === 0) {
            return;
        }
        $service_name = Service::find($connection['service_id'])->only('name')['name'];
        $personal_account = $connection['personal_account'];

        return [
            'id' => $this->id,
            'balance' => $this->balance_change,
            'date' => Carbon::parse($this->created_at)->format('d.m.Y'),
            'personal_account' => $personal_account,
            'service_name' => $service_name,
        ];
    }
}
