<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\Contact;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Http\Resources\MobileClientResource;
use App\Http\Resources\MobileServicesResource;
use App\Message;
use App\MobileService;
use App\Phone;
use Illuminate\Http\Request;
use Mobizon\MobizonApi;

class MobileController extends Controller {

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

    public function getClientData(Client $client) {
        $client_info = new MobileClientResource($client);
        return $client_info;
    }

    private function sendMessage($phone, $code) {

        $api = new MobizonApi('kze2629bf716d2bff8b28880881e32bdc4e89b33526f76cad0aad9af7c45d964a73444', 'api.mobizon.kz');
        $_phone = '7' . substr($phone, 1);
        $api->call('message', 'sendSMSMessage', array('recipient' => $_phone, 'text' => "Код подтверждения ELECOR: " . $code));
        return true;
    }

    public function messages(Client $client) {
        return MessageResource::collection($client->messages->sortByDesc('created_at'));
    }

    public function updateMessage(Message $message, Request $request) {
        $data = $request->all();
        $message->update($data);
    }

    public function getContacts() {
        return Contact::all()->first();
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
        $this->sendPush($message, $pushToken);
        $this->storeMessage(['client_id' => $client['id'], 'title' => $message['title'], 'body' => $message['body']]);
    }

    private function storeMessage($message) {
        $mailing_id = $this->getMailingId();
        $_message = $message;
        $_message['mailing_id'] = $mailing_id;
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

    private function sendPush($message, $pushToken = null) {
        $content = array("en" => $message['body']);

        $heading = ["en" => $message['title']];

        $segment = !$pushToken ? ['included_segments' => array('All')] : gettype($pushToken) === 'array' ? ['include_player_ids' => $pushToken] : ['include_player_ids' => [$pushToken]];

        $key = array_keys($segment)[0];

        $fields = array('app_id' => env('ONE_SIGNAL_APP_ID'), 'data' => array("foo" => "bar"), $key => $segment[$key], 'small_icon' => "ic_stat_onesignal_default.png", 'large_icon' => "ic_stat_onesignal_default.png", 'contents' => $content, 'headings' => $heading, 'android_group' => 'group');


        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic ' . env("ONE_SIGNAL_REST_API_KEY")));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function pay(Request $request) {
        $merchant_id = 514216;
        $secret_word = 'Vz4roXY8y3Ccxovs';

        $price = $request->get('price');
        $fullname = $request->get('name');
        $personal_id = $request->get('personal_id');
        $service_name = $request->get('service');

        // описание заказа
        $description = 'Оплата услуги "' . $service_name . '" для ' . $fullname . ' (Лицевой счет: ' . $personal_id . ')';

        $arrReq = array('pg_merchant_id' => $merchant_id, 'pg_amount' => $price, 'pg_salt' => mt_rand(21, 43433), 'pg_order_id' => 1345566, 'pg_description' => $description, 'pg_encoding' => 'UTF-8', 'pg_currency' => "KZT", //'pg_user_ip'        => $_SERVER['REMOTE_ADDR'],
            'pg_lifetime' => 86400, //'pg_request_method' => 'GET',
            'pg_success_url' => 'http://' . $_SERVER['SERVER_NAME'] . '/?install=success', 'pg_failure_url' => 'http://' . $_SERVER['SERVER_NAME'] . '/?install=error',);

        // $arrReq['pg_testing_mode'] = 1;

        $arrReq['pg_sig'] = $this->makes('payment.php', $arrReq, $secret_word);

        $query = http_build_query($arrReq);

        $url = 'https://api.paybox.money/payment.php?' . $query;

        return $url;

    }

    private function makes($scriptName, $arrReq, $secret_word) {
        ksort($arrReq);

        array_unshift($arrReq, $scriptName);
        array_push($arrReq, $secret_word);

        $sig = implode(';', $arrReq);

        return md5($sig);
    }
}
