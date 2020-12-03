<?php

namespace App\Http\Controllers;

use App\Client;
use App\Http\Resources\SingleClientResource;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VueController extends Controller
{
    public function index() {
        return view('index');
    }

    public function test(Client $client, Request $request) {
       /* $_client = Client::whereId($client)->with(['type', 'phones', 'connections', 'connections.service', 'connections.transactions'])->first();*/
        return view('test', [
            'client' => (new SingleClientResource($client))->toArray($request),
        ]);
    }
}
