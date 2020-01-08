<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Request;

class   ClientsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'personal_accounts' => $this->connections->where('is_deleted', false)->pluck('personal_account'),
            'addresses' => $this->connections->where('is_deleted', false)->pluck('address'),
            'trademarks' => $this->connections->where('is_deleted', false)->pluck('trademark'),
        ];
    }
}
