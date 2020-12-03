<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ClientType
 *
 * @property int $id
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClientType extends Model
{

}
