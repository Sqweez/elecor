<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtConnectionResource extends JsonResource
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
            'personal_account' => $this->personal_account,
            'trademark' => $this->trademark,
            'address' => $this->address,
            'debt' => +$this->balance,
            'service_id' => $this->service_id
        ];
    }
}
