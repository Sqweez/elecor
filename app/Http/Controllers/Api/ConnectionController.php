<?php

namespace App\Http\Controllers\Api;

use App\Connection;
use App\Http\Controllers\Controller;
use App\Http\Resources\ConnectionResource;
use App\Http\Resources\HistoryResource;
use App\Payment;
use App\Sale;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ConnectionController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request) {

        $data = $request->all();
        $connection = Connection::create($data)->id;
        $date_start = Carbon::parse($data['date_start']);
        if (!($date_start > Carbon::today()->endOfMonth())) {
            Payment::create(['connection_id' => $connection, 'price' => $data['month_price']]);
            Transaction::create(['connection_id' => $connection, 'balance_change' => $data['month_price'] * -1, 'user_id' => $data['user_id']]);
        }
        return new ConnectionResource(Connection::find($connection));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Connection $connection
     * @param Request $request
     * @return void
     */
    public function update(Connection $connection, Request $request) {
        $data = $request->all();
        $data['personal_account'] = preg_replace('/\s+/', '', $data['personal_account']);
        $connection->update($data);
        return $connection;
    }

    public function destroy(Connection $connection) {
        $connection->delete();
    }

    public function getDuplicate(Request $request) {
        $account = $request->get('personal_account');
        $result = Connection::where('personal_account', $account)->get();
        return count($result);
    }

    public function connect(Connection $connection) {
        $connection->update(['is_active' => true]);
        return $connection;
    }

    public function disconnect(Connection $connection) {
        $connection->update(['is_active' => false]);
        return $connection;
    }

    public function addBalances(Request $request) {
        $balances = json_decode($request->get('balances'), true);
        foreach ($balances as $balance) {
            Transaction::create(['connection_id' => $balance['id'], 'balance_change' => $balance['balance'], 'user_id' => 1]);
        }
    }

    public function addBalance(Connection $connection, Request $request) {
        $balance = $request->all();
        $balance['connection_id'] = $connection['id'];
        return Transaction::create($balance);
    }

    public function makeSale(Request $request) {
        $sale = $request->all();
        $sale_id = Sale::create($sale)->id;
        Payment::create(['connection_id' => $sale['connection_id'], 'price' => $sale['price'], 'sale_id' => $sale_id]);
        Transaction::create(['connection_id' => $sale['connection_id'], 'balance_change' => $sale['price'] * -1, 'sale_id' => $sale_id, 'user_id' => $sale['user_id']]);
    }

    public function history(Connection $connection) {
        $history = new HistoryResource($connection);
        return $history;
    }

    public function findAccount(Request $request) {
        $account = $request->get('account');
        $account = preg_replace('/\s+/', '', $account);
        $account = Connection::where('personal_account', $account)->with('client')->first();
        return $account;
    }

}
