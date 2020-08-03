<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\ClientType;
use App\Connection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\PushService;
use App\Http\Resources\ClientsResource;
use App\Http\Resources\ConnectionResource;
use App\Http\Resources\DebtResource;
use App\Http\Resources\SingleClientResource;
use App\Message;
use App\Payment;
use App\Phone;
use App\Service;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index() {

        return ClientsResource::collection(Client::with('type')->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return ClientsResource
     */
    public function store(Request $request) {
        $phones = $request->get('phones');
        $client = $request->except('phones');
        if (!$client['photo']) {
            $client = $request->except(['phones', 'photo']);
        }
        $client_id = $this->createClient($client, $phones);

        return new ClientsResource(Client::find($client_id));
    }

    private function createPhones($id, $phones) {
        foreach ($phones as $phone) {
            $data = ['phone' => $phone, 'client_id' => $id];
            Phone::create($data);
        }
    }

    private function createClient($client, $phones): int {
        $client_id = Client::create($client)->id;
        if (count($phones) > 0) {
            $this->createPhones($client_id, $phones);
        }
        return $client_id;
    }

    /**
     * Display the specified resource.
     *
     * @param Client $client
     * @return SingleClientResource
     */
    public function show(Client $client) {
        return new SingleClientResource($client);
    }

    public function deleteTransaction($id) {
        $transaction = Transaction::find($id);
        $transaction->delete();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Client $client
     * @param \Illuminate\Http\Request $request
     * @return SingleClientResource
     */
    public function update(Client $client, Request $request) {
        $data = $request->except('phones');
        $client->update($data);
        $client_id = $data['id'];
        $phones = $request->get('phones');
        $this->updatePhones($client_id, $phones);
        return new SingleClientResource($client);
    }

    private function updatePhones($id, $phones) {
        Phone::where('client_id', $id)->delete();
        foreach ($phones as $phone) {
            $data = ['phone' => $phone, 'client_id' => $id];
            Phone::create($data);
        }
    }

    public function updateClient(Client $client, Request $request) {
        $data = $request->all();
        $result = $client->update($data);
        return $client;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id) {
        $client = Client::find($id);
        $this->deletePhoto($client['photo']);
        $conn = Connection::where('client_id', $id);
        $conn->delete();
        $client->delete();
        return null;
    }

    private function deletePhoto($url) {
        if ($url === 'uploads/no_client_photo.jpg') {
            return null;
        }
        Storage::disk('public')->delete($url);
    }

    public function getTypes() {
        return ClientType::all();
    }

    public function createClients(Request $request) {
        $data = $request->get('clients');
        $clients = json_decode($data, true);
        foreach ($clients as $client) {
            $phones = $client['phones'];
            unset($client['phones']);
            $this->createClient($client, $phones);
        }
    }

    public function test() {
        $activeConnections = ConnectionResource::collection(Connection::all()->where('is_active', true));
        $activeConnections = $activeConnections->toArray($activeConnections);
        foreach ($activeConnections as $activeConnection) {
            if (count($activeConnection['payments']) === 0) {
                $date_start = Carbon::parse($activeConnection['date_start']);
                if ($date_start > Carbon::now()->startOfMonth() && $date_start <= Carbon::now()->endOfMonth()) {
                    $activeConnection['price'] = $activeConnection['month_price'];
                }
                Payment::create(['connection_id' => $activeConnection['id'], 'price' => $activeConnection['price']]);
                Transaction::create(['connection_id' => $activeConnection['id'], 'balance_change' => $activeConnection['price'] * -1, 'user_id' => 0]);
                $message = [
                    'title' => 'Внимание',
                    'body' => 'С Вашего баланса произошло списание ' . $activeConnection['price'] . ' тг по услуге ' . $activeConnection['service_name'] . '.!'
                ];

                $token = PushService::getToken($activeConnection['client_id']);

                if (strlen($token) > 0) {
                    PushService::sendPush($message, $token);
                }

            }
        }
    }

    public function push(Request $request) {
        $pushMessage = $request->except(['push_token']);
        $isGuest = $request->get('isGuest');
        $push_token = $request->get('push_token');
        $client_id = $request->get('client_id');
        $pushToken = null;
        if (!$isGuest) {
            $pushToken = PushService::getToken($client_id);
        } else if ($push_token) {
            $pushToken = $push_token;
        }

        PushService::sendPush($pushMessage, $pushToken);
        if ($isGuest) {
            return 1;
        }
        return $this->storeMessage($pushMessage);
    }

    public function pushes() {

    }

    private function storeMessage($message) {
       //$mailing_id = $this->getMailingId();
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


    public function parseClients() {
        $clientsFile = Storage::disk('public')->get('users.json');

        $clientsData = json_decode($clientsFile, true);

        $services = Service::all()->toArray();

        $serviceNames = array_map(function ($i) {
            return $i['name'];
        }, $services);

        $clientsData = array_map(function ($i) use ($serviceNames, $services) {
            $keys = array_keys($i);
            $index = count($i) - 1;
            $service = $keys[$index];
            $service_key = array_search($service, $serviceNames);
            $service_id = $services[$service_key]['id'];
            $i['date_start'] = Carbon::parse($i['dogovordatanach'])->format('Y-m-d');
            $i['service_id'] = $service_id;
            $i['trademark'] = $services[$service_key]['trademark_default'];
            $i['client_type'] = $i['vidklienta'] === 'Юридические лица' ? 3 : 1;
            $i['price'] = intval($i[$service]);;
            $i['balans'] = intval($i['balans']);
            return $i;
        }, $clientsData);


        foreach ($clientsData as $clientsDatum) {
            $name = $clientsDatum['klient'];
            $client = Client::where('name', $name)->first();
            $client_id = null;
            if (!$client) {
                $client = ['name' => $name, 'client_type' => $clientsDatum['client_type']];

                if (array_key_exists('Телефон', $clientsDatum)) {
                    $phone = [str_replace(' ', '', $clientsDatum['Телефон'])];
                } else {
                    $phone = [];
                }
                $client_id = $this->createClient($client, $phone);
            } else {
                $client_id = $client->id;
            }

            $connection = [
                'client_id' => $client_id,
                'user_id' => 1,
                'service_id' => $clientsDatum['service_id'],
                'address' => $clientsDatum['ulica'] . " " . $clientsDatum['zdanie'],
                'trademark' => $clientsDatum['trademark'],
                'personal_account' => $clientsDatum['licshet'],
                'price' => $clientsDatum['price'],
                'date_start' => $clientsDatum['date_start']
            ];

            $connection_id = Connection::create($connection)->id;

            if ($clientsDatum['balans'] !== 0) {
                $transaction = [
                    'connection_id' => $connection_id,
                    'balance_change' => $clientsDatum['balans'],
                    'user_id' => 1,
                    'is_visible' => false
                ];

                Transaction::create($transaction);

            }
        }

    }

    public function getDebt() {
        $debts = DebtResource::collection(Client::all());
        $total_debt = 0;

        $debts = collect($debts)->filter(function($i) {
            return $i !== null;
        })->values();

        $total_debt = collect($debts)->reduce(function ($a, $c) {
            return $a + collect($c['connections'])->reduce(function ($a, $c) {
                return $a + $c['debt'];
            }, 0);
        }, 0);

        $data = [
            'debts' => $debts,
            'total_debt' => $total_debt
        ];

        return $data;

    }

    public function clear() {
        Client::truncate();
        Transaction::truncate();
        Connection::truncate();
        Phone::truncate();
        Payment::truncate();
    }

}
