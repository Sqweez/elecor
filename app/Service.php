<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $guarded = [];

    public function oneTimeService() {
        return $this->hasMany('App\Service', 'main_id', 'id');
    }
}
