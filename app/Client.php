<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Client
 *
 * @property int $id
 * @property string $name
 * @property string|null $birth_date
 * @property int|null $client_type
 * @property string $photo
 * @property string $push_token
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $additional_fields
 * @property string $gender
 * @property string $lang
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Connection[] $connections
 * @property-read int|null $connections_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Message[] $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Phone[] $phones
 * @property-read int|null $phones_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @property-read \App\ClientType|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereAdditionalFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereClientType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePushToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
