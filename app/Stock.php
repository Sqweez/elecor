<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $guarded = [];

    public function service() {
        return $this->belongsTo('App\MobileService', 'service_id');
    }
}
