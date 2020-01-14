<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = [];

    public function user_role() {
        return $this->hasOne('App\Role', 'id', 'role');
    }
}
