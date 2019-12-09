<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    protected $fillable = ['phone', 'client_id'];

    public function client() {
        $this->belongsTo('App\Client', 'client_id');
    }
}
