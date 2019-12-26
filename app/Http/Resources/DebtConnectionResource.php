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

        $balance = $this->transactions->sum('balance_change');

        if ($balance >= 0) {
            return null;
        }

        return [
            'id' => $this->id,
            'personal_account' => $this->personal_account,
            'trademark' => $this->trademark,
            'address' => $this->address,
            'debt' => $this->transactions->sum('balance_change'),
        ];
    }
}
