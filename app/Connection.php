<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

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


    public static function boot() {
        parent::boot();
        static::deleting(function ($connection) {
            $connection->payments()->delete();
            $connection->transactions()->delete();
        });
    }

}
