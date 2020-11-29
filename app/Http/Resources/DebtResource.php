<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

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
        $connections = DebtConnectionResource::collection($this->connections);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'connections' => $connections,
            'phones' => $this->phones->map(function($i) {
                return $i->phone;
            })->values(),

            '_personalAccounts' => trim($this->connections->reduce(function ($a, $c) {
                return $a . " " . $c->personal_account;
            }, '')),
            '_addresses' => trim($this->connections->reduce(function ($a, $c) {
                return $a . " " . $c->address;
            }, '')),
            '_trademarks' => trim($this->connections->reduce(function ($a, $c) {
                return $a . " " . $c->trademark;
            }, ''))
        ];
    }
}
