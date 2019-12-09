<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Connection extends Model {
    protected $guarded = [];

    public function payments() {
        return $this->hasMany('App\Payment');
    }

    public function transactions() {
        return $this->hasMany('App\Transaction');
    }

    public function client() {
        return $this->belongsTo('App\Client', 'client_id');
    }

    public static function boot() {
        parent::boot();
        static::deleting(function($connection) {
            $connection->payments()->delete();
            $connection->transactions()->delete();
        });
    }

}
