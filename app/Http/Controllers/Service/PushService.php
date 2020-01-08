<?php


namespace App\Http\Controllers\Service;


use App\Client;

class PushService {
    public function __construct() {}

    public static function sendPush($message, $token = null) {
        $content = array(
            "en" => $message['body']
        );

        $heading = [
            "en" => $message['title']
        ];

        $segment = !$token
            ? ['included_segments' => array('All')]
            : gettype($token) === 'array'
                ? ['include_player_ids' => $token]
                : ['include_player_ids' => [$token]];

        $key = array_keys($segment)[0];

        $fields = array(
            'app_id' => env('ONE_SIGNAL_APP_ID'),
            'data' => array("foo" => "bar"),
            $key => $segment[$key],
            'small_icon' => "ic_stat_onesignal_default.png",
            'large_icon' =>"ic_stat_onesignal_default.png",
            'contents' => $content,
            'headings' => $heading,
            'android_group' => 'group'
        );


        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . env("ONE_SIGNAL_REST_API_KEY")));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public static function getToken($id) {
        $push_token = Client::find($id)['push_token'];
        return $push_token;
    }
}
