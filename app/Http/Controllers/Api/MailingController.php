<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\PushService;
use App\MailingHistory;
use App\Message;
use App\Phone;
use Illuminate\Http\Request;
use Mobizon\MobizonApi;

class MailingController extends Controller {

    const SEND_ALL_TYPES = 0;
    const SEND_SMS = 1;
    const SEND_PUSH = 2;

    const NAME = '%ИМЯ%';

    public function mailing(Request $request) {
        $clients = $request->get('clients');
        $sendType = intval($request->get('sendType'));
        $title = $request->get('title');
        $body = $request->get('body');

        $mailing_id = MailingHistory::create(['title' => $title, 'body' => $body, 'user_id' => 0])['id'];

        if ($sendType === self::SEND_ALL_TYPES) {
            $this->sendAll($clients, $title, $body, $mailing_id);
        }

        if ($sendType === self::SEND_SMS) {
            $this->sendSMS($clients, $title, $body, $mailing_id);
        }

        if ($sendType === self::SEND_PUSH) {
            $this->sendPush($clients, $title, $body, $mailing_id);
        }

    }

    private function sendAll($ids, $title, $body, $mailing_id) {
        foreach ($ids as $id) {
            $client = Client::find($id);
            $message = $this->parseTemplate($client, $title, $body);
            if ($client['push_token']) {
                $this->push($message, $client['push_token']);
            } else {
                $phone = Phone::where('client_id', $id)->first()['phone'];
                $this->sms($message, $phone);
            }
            $this->storeMessage($message, $mailing_id, $id);
        }
    }

    private function sendSMS($ids, $title, $body, $mailing_id) {
        foreach ($ids as $id) {
            $client = Client::find($id);
            $message = $this->parseTemplate($client, $title, $body);
            $phone = Phone::where('client_id', $id)->first()['phone'];
            $this->sms($message, $phone);
            $this->storeMessage($message, $mailing_id, $id);
        }
    }

    private function sendPush($ids, $title, $body, $mailing_id) {
        foreach ($ids as $id) {
            $client = Client::find($id);
            $message = $this->parseTemplate($client, $title, $body);
            if ($client['push_token']) {
                $this->push($message, $client['push_token']);
                $this->storeMessage($message, $mailing_id, $id);
            }
        }
    }

    private function storeMessage($message, $id, $client_id) {
        Message::create(['title' => $message['title'], 'body' => $message['body'], 'user_id' => 0, 'client_id' => $client_id, 'mailing_id' => $id]);
    }

    private function parseTemplate($client, $_title, $_body) {
        $title = str_replace(self::NAME, $client['name'], $_title);
        $body = str_replace(self::NAME, $client['name'], $_body);
        return ['title' => $title, 'body' => $body];
    }

    private function push($message, $token) {
        PushService::sendPush($message, $token);
    }

    private function sms($message, $phone) {
        $api = new MobizonApi(env('MOBIZON_KEY'), 'api.mobizon.kz');
        $_phone = '7' . substr($phone, 1);
        $api->call('message', 'sendSMSMessage', array('recipient' => $_phone, 'text' => $message['body'], 'from' => 'ELECOR'));
        return true;
    }
}
