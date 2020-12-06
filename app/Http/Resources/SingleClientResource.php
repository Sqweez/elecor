<?php

namespace App\Http\Resources;

use App\Client;
use App\ClientType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     * @mixin Client
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'has_push_token' => !!$this->push_token,
            'name' => $this->name,
            'comment' => $this->comment,
            'client_type' => intval($this->client_type),
            'photo' => $this->photo,
            'type' => $this->type->type,
            'phones' => $this->phones->pluck('phone'),
            'connections' => ConnectionResource::collection($this->connections->where('is_deleted', false)->except(['created_at', 'updated_at'])),
            'gender' => $this->gender,
            'lang' => $this->lang,
            'gender_text' => Client::GENDERS[$this->gender]['gender'],
            'lang_text' => Client::LANGUAGES[$this->lang]['lang'],
            'birth_date' => $this->birth_date ?? null,
            'physical_person' => $this->client_type === ClientType::CLIENT_TYPE_PHYSICAL,
            'bonus_transactions' => BalanceTransactionResource::collection($this->bonus_transactions->sortByDesc('created_at')),
            'bonuses' => $this->bonus_transactions->sum('amount'),
        ];
    }
}
