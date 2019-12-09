<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\Contact;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Http\Resources\MobileClientResource;
use App\Http\Resources\MobileServicesResource;
use App\Http\Resources\SingleClientResource;
use App\Message;
use App\MobileService;
use App\Phone;
use Illuminate\Http\Request;
use Mobizon\MobizonApi;

class MobileController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

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

        $_phone = '7' . substr($phone, 1);

        $url = "https://api.mobizon.kz/service/message/sendSmsMessage?output=json&api=v1&apiKey=kzcfc1bce637a2d0c9191444d06da1c39394e999c69e806544b254af3afb5f8288d11c&recipient=77059864410&text=Пароль для мобильного приложения";

        $params = "&recipient=77059864410&text=Пароль для мобильного приложения";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//для возврата результата в виде строки, вместо прямого вывода в браузер
        $returned = curl_exec($ch);
        curl_close ($ch);

        return $returned;

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
}
