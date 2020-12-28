<?php

namespace App;

use App\Http\Controllers\Service\PushService;
use Illuminate\Database\Eloquent\Model;

class RecurrentPayments extends Model
{
    protected $fillable = ['connection_id', 'recurring_profile_id', 'recurring_profile_expiry_date', 'client_id'];

    protected static function boot() {
        parent::boot();

        parent::created(function($recurrentPayment) {
                $token = PushService::getToken($recurrentPayment->client_id);
                if ($token) {
                    PushService::sendPush([
                        'title' => 'Новое сообщение',
                        'body' => 'Вы успешно подключены к системе ежемесячного списания платежей'
                    ], $token);
                }
            });
    }
}
