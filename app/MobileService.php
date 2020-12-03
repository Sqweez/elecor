<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MobileService
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string|null $icon
 * @property string|null $image
 * @property string|null $additional_information
 * @property int|null $main_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MobileService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MobileService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MobileService query()
 * @method static \Illuminate\Database\Eloquent\Builder|MobileService whereAdditionalInformation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MobileService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MobileService whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MobileService whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MobileService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MobileService whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MobileService whereMainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MobileService whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MobileService whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MobileService extends Model
{
    protected $guarded = [];
}
