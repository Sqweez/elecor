<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MailingHistory
 *
 * @property int $id
 * @property string $title
 * @property string|null $body
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Message[] $clients
 * @property-read int|null $clients_count
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|MailingHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingHistory whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingHistory whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingHistory whereUserId($value)
 * @mixin \Eloquent
 */
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
