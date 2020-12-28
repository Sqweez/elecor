<?php

namespace App\Http\Controllers\Api\v2;

use App\BonusTransaction;
use App\Client;
use App\Company;
use App\Contact;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\PushService;
use App\Http\Resources\MessageResource;
use App\Http\Resources\MobileClientResource;
use App\Http\Resources\MobileServicesResource;
use App\Message;
use App\MobileService;
use App\OnlinePayment;
use App\Phone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mobizon\MobizonApi;

class MobileController extends Controller
{
    public function sms(Request $request) {
        $phone = $request->get('phone');
        $clientPhone = Phone::where('phone', $phone)->first();
        if (!$clientPhone) {
            return ['error' => 'Клиент не найден'];
        }

        $code = mt_rand(1000, 9999);

        $this->sendMessage($phone, $code);
        $client = Client::find($clientPhone['client_id'])->only(['id']);
        return ['code' => $code, 'client_id' => $client['id']];
    }

    public function getPayboxCompanies(Request $request) {
        return Company::payable()->select([
            'id', 'name', 'can_recurr'
        ])->get();
    }

    public function getClientData(Client $client) {
        return [
            'id' => $client->id,
            'push_token' => $client->push_token,
            'name' => $client->name,
            'phone' => $client->phones[0]->phone ?? '',
            'bonuses' => $client->bonuses_sum,
            'connections' => $client->connections->where('is_active', 1)->map(function ($connection) {
                return [
                    'id' => $connection['id'],
                    'service_id' => intval($connection['service']['id']),
                    'service_name' => $connection['service']['name'],
                    'personal_account' => $connection['personal_account'],
                    'balance' => intval($connection['transactions']->sum('balance_change')),
                    'service_id' => $connection['service_id'],
                    'month_price' => $connection['price'] == 0 ? $connection['service']['price'] : $connection['price'],
                    'additional' => [
                        [
                            'name' => 'Лицевой счет',
                            'value' => $connection['personal_account'],
                        ],
                        [
                            'name' => 'Баланс',
                            'value' => intval($connection['transactions']->sum('balance_change'))
                        ],
                        [
                            'name' => 'Договор заключен:',
                            'value' => $connection['company']['name']
                        ]
                    ],

                ];
            })->values()->all(),
            'transactions' => $client->transactions
                ->where('is_visible', true)
                ->sortByDesc('created_at')
                ->values()
                ->map(function ($transaction) {
                    return [
                        'id' => $transaction['id'],
                        'balance' => $transaction['balance_change'],
                        'date' => Carbon::parse($transaction['created_at'])->format('d.m.Y'),
                        'personal_account' => $transaction['connection']['personal_account'],
                        'service_name' => $transaction['connection']['service']['name']
                    ];
                }),
        ];
    }

    private function sendMessage($phone, $code) {

        $api = new MobizonApi(env('MOBIZON_KEY'), 'api.mobizon.kz');
        $_phone = '7' . substr($phone, 1);
        $api->call('message', 'sendSMSMessage', array('recipient' => $_phone, 'text' => "Код подтверждения ELECOR: " . $code, 'from' => 'ELECOR'));
        return true;
    }

    public function messages(Client $client) {
        return MessageResource::collection($client->messages->sortByDesc('created_at'));
    }

    public function deleteMessage($id) {
        $message = Message::find($id);
        $message->delete();
    }

    public function updateMessage(Message $message, Request $request) {
        $data = $request->all();
        $message->update($data);
    }

    public function getContacts() {
        return Contact::first();
    }

    public function updateContacts(Contact $contact, Request $request) {
        $data = $request->all();
        $contact->update($data);
    }

    public function getServices(Request $request) {
        $mobile = $request->get('mobile');
        if ($mobile) {
            return MobileServicesResource::collection(MobileService::all());
        }
        return MobileService::all();
    }

    public function createService(Request $request) {
        $data = $request->all();
        return MobileService::create($data);
    }

    public function deleteService($id) {
        $service = MobileService::find($id);
        $service->delete();
    }

    public function editService($id, Request $request) {
        $data = $request->all();
        $service = MobileService::find($id);
        $service->update($data);
    }

    public function welcome(Client $client) {
        $message = ['title' => 'Добро пожаловать!', 'body' => 'Добро пожаловать в приложение Клиент ОА «ELECOR»!'];
        $pushToken = $client['push_token'];
        if (!$pushToken) {
            return response()->json([], 200);
        }
        PushService::sendPush($message, $pushToken);
        $this->storeMessage(['client_id' => $client['id'], 'title' => $message['title'], 'body' => $message['body']]);
    }

    private function storeMessage($message) {
        $_message = $message;
        $_message['mailing_id'] = 0;
        $mailing = Message::create($_message);
        return $mailing;
    }

    private function getMailingId(): int {
        $last_message = Message::all()->last();
        if (!$last_message) {
            $mailing_id = 1;
        } else {
            $mailing_id = $last_message['mailing_id'] + 1;
        }
        return $mailing_id;
    }

    public function pay(Request $request) {
        // @TODO: подумать по бонусам, списывать ли их сразу или нет
        $company = Company::find($request->get('company'));
        $merchant_id = $company->paybox_id;
        $secret_word = $company->paybox_secret_word;
        $bonuses = intval($request->get('bonuses'));
        $connection_id = intval($request->get('connection_id'));
        $price = intval($request->get('price'));
        $fullname = $request->get('name');
        $personal_id = $request->get('personal_id');
        $service_name = $request->get('service');
        $client_id = $request->get('client_id');
        $phone = Phone::whereClientId($client_id)->first();
        $recurring_agree = !!$request->get('recurring_agree', false);
        $bonus_transaction_id = null;

        // описание заказа
        $description = 'Оплата услуги "' . $service_name . '" для ' . $fullname . ' (Лицевой счет: ' . $personal_id . ')';

        if ($bonuses > 0) {
            $referralController = new ReferralController();
            $bonus_transaction_id = $referralController->createBonusOperation(
                BonusTransaction::OPERATION_TYPE_DEBIT,
                $connection_id,
                0,
                $client_id,
                $bonuses,
                $description
            )->id;
        }

        $onlinePayment = [
            'amount' => $price - $bonuses,
            'bonuses' => $bonuses,
            'client_id' => $client_id,
            'company_id' => $company->id,
            'connection_id' => $connection_id,
            'description' => $description,
            'status' => OnlinePayment::STATUS_AWAITING
        ];

        if ($bonus_transaction_id !== null) {
            $onlinePayment['bonus_transaction_id'] = $bonus_transaction_id;
        }


        $online_payment_id = OnlinePayment::create($onlinePayment)->id;

        $arrReq = [
            'pg_merchant_id' => $merchant_id,
            'pg_amount' => $price - $bonuses,
            'pg_salt' => mt_rand(21, 43433),
            'pg_order_id' => $online_payment_id,
            'pg_description' => $description,
            'pg_encoding' => 'UTF-8',
            'pg_currency' => "KZT",
            'pg_lifetime' => 86400,
            'pg_result_url' => 'https://' . $_SERVER['SERVER_NAME'] . '/api/v2/payments/online/check',
            'pg_testing_mode' => 1,
            'online_payment_id' => $online_payment_id,
            'pg_user_phone' => '+7' . substr($phone->phone, 1),
            'client_id' => $client_id
        ];

        if ($recurring_agree) {
            $arrReq['pg_recurring_start'] = 1;
            $arrReq['pg_recurring_lifetime'] = 24;
            $arrReq['pg_user_id'] = $client_id;
        }

        $arrReq['pg_sig'] = $this->makes('payment.php', $arrReq, $secret_word);

        $query = http_build_query($arrReq);

        return 'https://api.paybox.money/payment.php?' . $query;

    }

    private function makes($scriptName, $arrReq, $secret_word) {
        ksort($arrReq);

        array_unshift($arrReq, $scriptName);
        array_push($arrReq, $secret_word);

        $sig = implode(';', $arrReq);

        return md5($sig);
    }
}
