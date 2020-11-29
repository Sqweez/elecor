<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Request;

class ClientsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $connections = collect($this->connections->where('is_deleted', false));

        return [
            'id' => $this->id,
            'name' => $this->name,
            'personal_accounts' => $connections->pluck('personal_account'),
            'addresses' => $connections->pluck('address'),
            'trademarks' => $connections->pluck('trademark'),
            'client_type' => intval($this->client_type)
        ];
    }
}
