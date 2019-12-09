<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    public function client() {
        return $this->belongsTo('App\Client', 'client_id');
    }

    public function service() {
        return $this->belongsTo('App\MobileService', 'service_id');
    }
}
