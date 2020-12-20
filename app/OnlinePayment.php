<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\OnlinePayment
 *
 * @property int $id
 * @property int $amount
 * @property int $bonuses
 * @property int $client_id
 * @property int $company_id
 * @property int $connection_id
 * @property int $bonus_transaction_id
 * @property string|null $description
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OnlinePayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlinePayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlinePayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlinePayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlinePayment whereBonusTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlinePayment whereBonuses($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlinePayment whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlinePayment whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlinePayment whereConnectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlinePayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlinePayment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlinePayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlinePayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlinePayment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OnlinePayment extends Model
{
    const STATUS_AWAITING = 0;
    const STATUS_CONFIRMED = 1;
    const STATUS_REJECTED = -1;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'integer'
    ];
}
