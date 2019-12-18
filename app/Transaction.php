<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {
    protected $guarded = [];

    public function sales() {
        return $this->belongsTo('App\Sale', 'sale_id');
    }


    public function client() {

    }
}
