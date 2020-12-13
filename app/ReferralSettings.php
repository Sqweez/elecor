<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\ReferralSettings
 *
 * @property int $id
 * @property string|null $message_template
 * @property string|null $base_url
 * @property int|null $discount
 * @property boolean $hash_ref
 * @method static Builder|ReferralSettings newModelQuery()
 * @method static Builder|ReferralSettings newQuery()
 * @method static Builder|ReferralSettings query()
 * @method static Builder|ReferralSettings whereBaseUrl($value)
 * @method static Builder|ReferralSettings whereDiscount($value)
 * @method static Builder|ReferralSettings whereHashRef($value)
 * @method static Builder|ReferralSettings whereId($value)
 * @method static Builder|ReferralSettings whereMessageTemplate($value)
 * @mixin \Eloquent
 * @property int $cashback
 * @method static Builder|ReferralSettings whereCashback($value)
 */
class ReferralSettings extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public function getHashRefAttribute($value) {
        return !!$value;
    }

}
