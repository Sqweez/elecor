<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\PushService;
use App\OnlinePayment;
use App\RecurrentPayments;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    const PAYBOX_OK = 1;
    public function checkOnlinePayment(Request $request) {
        $resultCode = intval($request->get('pg_result', 0));
        $onlinePaymentId = $request->get('online_payment_id');
        $recurringProfileId = $request->get('pg_recurring_profile_id', null);
        $recurringProfileExpiryDate = $request->get('pg_recurring_profile_expiry_date', null);

        $onlinePayment = OnlinePayment::find($onlinePaymentId);
        if ($resultCode === self::PAYBOX_OK) {
            $onlinePayment->status = OnlinePayment::STATUS_CONFIRMED;
        } else {
            $onlinePayment->status = OnlinePayment::STATUS_REJECTED;
        }
        $onlinePayment->save();

        if ($recurringProfileId !== null) {
            RecurrentPayments::create([
                'client_id' => $onlinePayment->client_id,
                'connection_id' => $onlinePayment->connection_id,
                'recurring_profile_id' => $recurringProfileId,
                'recurring_profile_expiry_date' => $recurringProfileExpiryDate
            ]);
        }
    }
}
