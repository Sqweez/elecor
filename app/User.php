<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = [];

    public function user_role() {
        return $this->belongsTo('App\Role', 'role');
    }
}
