<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * App\Connection
 *
 * @property int $id
 * @property int $client_id
 * @property int $user_id
 * @property int $service_id
 * @property string|null $address
 * @property string $trademark
 * @property string $personal_account
 * @property int $balance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $price
 * @property int $month_price
 * @property string $date_start
 * @property int $is_active
 * @property string|null $disable_date
 * @property int $is_deleted
 * @property int $company_id
 * @property-read \App\Client $client
 * @property-read \App\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read \App\Service $service
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Connection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Connection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Connection query()
 * @method static \Illuminate\Database\Eloquent\Builder|Connection whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Connection whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Connection whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Connection whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Connection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Connection whereDateStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Connection whereDisableDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Connection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Connection whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Connection whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Connection whereMonthPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Connection wherePersonalAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Connection wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Connection whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Connection whereTrademark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Connection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Connection whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|Connection account($account = '')
 */
class Connection extends Model {
    protected $guarded = [];

    public function payments() {
        return $this->hasMany('App\Payment');
    }

    public function transactions() {

        return $this->hasMany('App\Transaction');
    }

    public function company() {
        return $this->belongsTo('App\Company', 'company_id');
    }

    public function client() {
        return $this->belongsTo('App\Client', 'client_id');
    }

    public function service() {
        return $this->belongsTo('App\Service', 'service_id');
    }

    public function scopeAccount($query, $account = "") {
        return $query->where('personal_account', $account);
    }


    public static function boot() {
        parent::boot();
        static::deleting(function ($connection) {
            $connection->payments()->delete();
            $connection->transactions()->delete();
        });
    }

}
