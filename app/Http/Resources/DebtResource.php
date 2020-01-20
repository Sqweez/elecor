<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DebtResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $connections = DebtConnectionResource::collection($this->connections->where('is_deleted', '0'))->toArray($request);

        $connections = array_filter($connections, function ($arr) {
            return $arr !== null;
        });

        if (count($connections) === 0) {
            return null;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'connections' => $connections,
            '_personalAccounts' => join(' ', array_map(function ($i) {
                return $i['personal_account'];
            }, $connections)),
            '_addresses' => join(' ', array_map(function ($i) {
                return $i['address'];
            }, $connections)),
            '_trademarks' => join(' ', array_map(function ($i) {
                return $i['trademark'];
            }, $connections)),
        ];
    }
}
