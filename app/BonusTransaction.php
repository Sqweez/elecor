<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\BonusTransaction
 *
 * @property int $id
 * @property int $client_id
 * @property int $amount
 * @property int $connection_id
 * @property int $operation_type
 * @property string|null $comment
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Client $client
 * @property-read \App\Connection $connection
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|BonusTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BonusTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BonusTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|BonusTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BonusTransaction whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BonusTransaction whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BonusTransaction whereConnectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BonusTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BonusTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BonusTransaction whereOperationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BonusTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BonusTransaction whereUserId($value)
 * @mixin \Eloquent
 */
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
