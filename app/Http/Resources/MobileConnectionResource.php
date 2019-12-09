<?php

namespace App\Http\Resources;

use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileConnectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $service_id = $this->service_id;

        $service_name = Service::find($service_id)->only('name')['name'];

        return [
            'id' => $this->id,
            'personal_account' => $this->personal_account,
            'service_name' => $service_name,
            'balance' => $this->transactions->sum('balance_change'),
        ];
    }
}
