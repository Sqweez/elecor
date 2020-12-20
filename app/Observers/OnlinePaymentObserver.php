<?php

namespace App\Observers;

use App\Http\Controllers\Service\PushService;
use App\OnlinePayment;
use App\Transaction;

class OnlinePaymentObserver
{
    /**
     * Handle the online payment "created" event.
     *
     * @param  \App\OnlinePayment  $onlinePayment
     * @return void
     */
    public function created(OnlinePayment $onlinePayment)
    {
        //
    }

    /**
     * Handle the online payment "updated" event.
     *
     * @param  \App\OnlinePayment  $onlinePayment
     * @return void
     */
    public function updated(OnlinePayment $onlinePayment)
    {
        if ($onlinePayment->status === OnlinePayment::STATUS_CONFIRMED) {
            $token = PushService::getToken($onlinePayment->client_id);
            if ($token) {
                PushService::sendPush([
                    'title' => 'Новое сообщение',
                    'body' => 'Ваш баланс пополнен на ' . $onlinePayment->amount . ' тенге'
                ], $token);
            }
            Transaction::create([
                'connection_id' => $onlinePayment->connection_id,
                'balance_change' => $onlinePayment->amount,
                'sale_id' => null,
                'user_id' => -1,
            ]);
        }
    }

    /**
     * Handle the online payment "deleted" event.
     *
     * @param  \App\OnlinePayment  $onlinePayment
     * @return void
     */
    public function deleted(OnlinePayment $onlinePayment)
    {
        //
    }

    /**
     * Handle the online payment "restored" event.
     *
     * @param  \App\OnlinePayment  $onlinePayment
     * @return void
     */
    public function restored(OnlinePayment $onlinePayment)
    {
        //
    }

    /**
     * Handle the online payment "force deleted" event.
     *
     * @param  \App\OnlinePayment  $onlinePayment
     * @return void
     */
    public function forceDeleted(OnlinePayment $onlinePayment)
    {
        //
    }
}
