<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AdditionalField
 *
 * @property int $id
 * @property string $alias
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalField query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalField whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalField whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalField whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AdditionalField extends Model
{
    protected $guarded = [];
}
