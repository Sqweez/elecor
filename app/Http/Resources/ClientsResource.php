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
        $connections = collect($this->connections->where('is_deleted', false));

        $mtk_only = $request->has('variant') && $request->get('variant') == 4;

        $connections = $mtk_only ? $connections->filter(function ($i) {
            return $i['service_id'] == 5;
        }) : $connections;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'personal_accounts' => $connections->pluck('personal_account'),
            'addresses' => $connections->pluck('address'),
            'trademarks' => $connections->pluck('trademark'),
        ];
    }
}
