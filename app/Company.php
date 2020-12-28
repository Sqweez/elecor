<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Company
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company query()
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $paybox_id
 * @property string|null $paybox_secret_word
 * @property bool $can_pay
 * @method static \Illuminate\Database\Eloquent\Builder|Company payable()
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCanPay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePayboxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePayboxSecretWord($value)
 */
class Company extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'can_pay' => 'boolean',
        'can_recurr' => 'boolean'
    ];

    public function scopePayable($query) {
        return $query->where('paybox_id', '!=', null)
            ->where('paybox_secret_word', '!=', null)
            ->where('can_pay', true);
    }
}
