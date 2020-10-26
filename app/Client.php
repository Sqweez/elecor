<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model {
    protected $fillable = ['name', 'client_type', 'birth_date', 'comment', 'photo', 'push_token', 'additional_fields', 'lang', 'gender'];

    public function type() {
        return $this->belongsTo('App\ClientType', 'client_type');
    }
    public function phones() {
        return $this->hasMany('App\Phone');
    }

    public function connections() {
        return $this->hasMany('App\Connection', 'client_id', 'id');
    }

    public function transactions() {
        return $this->hasManyThrough('App\Transaction', 'App\Connection', 'client_id', 'connection_id');
    }

    public function messages() {
        return $this->hasMany('App\Message', 'client_id');
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($user) {
            $user->phones()->delete();
        });
    }

}
