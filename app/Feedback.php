<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Feedback
 *
 * @property int $id
 * @property int $client_id
 * @property string $feedback
 * @property int|null $user_id
 * @property string|null $answer
 * @property int $is_worked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Client $client
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback query()
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereFeedback($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereIsWorked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereUserId($value)
 * @mixin \Eloquent
 */
class Feedback extends Model
{
    protected $guarded = [];

    public function client() {
        return $this->belongsTo('App\Client', 'client_id');
    }
}
