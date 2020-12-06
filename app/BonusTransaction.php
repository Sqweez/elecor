<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BonusTransaction extends Model
{
    const OPERATION_TYPES = [
        1 => [
            'id' => 1,
            'name' => 'Пополнение',
            'amount' => 1
        ],
        2 => [
            'id' => 2,
            'name' => 'Списание',
            'min_bonus' => 0,
            'max_amount' => 'current_balance',
            'amount' => -1,
        ],
        3 => [
            'id' => 3,
            'name' => 'Обналичивание',
            'min_bonus' => 0,
            'max_amount' => 'current_balance',
            'amount' => -1
        ],
    ];

    const OPERATION_TYPE_CREDIT = 1;
    const OPERATION_TYPE_DEBIT = 2;
    const OPERATION_TYPE_CASH_OUT = 3;

    protected $fillable = ['client_id', 'amount', 'connection_id', 'operation_type', 'comment', 'user_id'];

    protected $casts = [
        'client_id' => 'integer',
        'amount' => 'integer',
        'connection_id' => 'integer',
        'operation_type' => 'integer',
        'user_id' => 'integer',
    ];

    public function user() {
        return $this->belongsTo('App\User')->withDefault(
            [
                'id' => -1,
                'name' => 'Система'
            ]
        );
    }

    public function connection() {
        return $this->belongsTo('App\Connection', 'connection_id')->withDefault([
            'trademark' => null,
            'account' => null,
            'address' => null
        ])->with('service')->withDefault([
            'name' => -1,
        ]);
    }

    public function client() {
        return $this->belongsTo('App\Client')->withDefault(
            [
                'id' => -1,
                'name' => 'Неизвестно'
            ]
        );
    }
}
