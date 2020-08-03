<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailingHistory extends Model
{
    protected $guarded = [];

    public function clients() {
        return $this->hasMany('App\Message', 'mailing_id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id')->withDefault(
            ['name' => 'Администратор']
        );
    }

}
