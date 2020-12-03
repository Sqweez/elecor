<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Stock
 *
 * @property int $id
 * @property string $title
 * @property string|null $body
 * @property string $image
 * @property int $is_visible
 * @property int|null $service_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\MobileService|null $service
 * @method static \Illuminate\Database\Eloquent\Builder|Stock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stock query()
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Stock extends Model
{
    protected $guarded = [];

    public function service() {
        return $this->belongsTo('App\MobileService', 'service_id');
    }
}
