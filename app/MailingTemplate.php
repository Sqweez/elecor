<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MailingTemplate
 *
 * @property int $id
 * @property string $name
 * @property string $title
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MailingTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingTemplate whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingTemplate whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingTemplate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MailingTemplate extends Model
{
    protected $guarded = [];
}
