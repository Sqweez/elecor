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
        $is_mtk = $request->has('mtk');
        $connections = $is_mtk ? DebtConnectionResource::collection(
            $this->connections->where('service_id', 5)->where('is_deleted', '0')
        )->toArray($request) : DebtConnectionResource::collection(
            $this->connections->where('is_deleted', '0')
        )->toArray($request);

        $connections = $is_mtk ? array_filter($connections, function ($arr) {
            return $arr !== null && intval($arr['service_id'] ) === 5;
        }) : array_filter($connections, function ($arr) {
            return $arr !== null;
        });


        if (count($connections) === 0) {
            return null;
        }

        $phones = $this->phones;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'connections' => $connections,
            'phones' => $phones->map(function($i) {
                return $i->phone;
            })->toArray(),
            '_personalAccounts' => join(' ', array_map(function ($i) {
                return $i['personal_account'];
            }, $connections)),
            '_addresses' => join(' ', array_map(function ($i) {
                return $i['address'];
            }, $connections)),
            '_trademarks' => join(' ', array_map(function ($i) {
                return $i['trademark'];
            }, $connections)),
            'personal_accounts' => collect($connections)->pluck('personal_account'),
            'addresses' => collect($connections)->pluck('address'),
            'trademarks' => collect($connections)->pluck('trademark'),
        ];
    }
}
