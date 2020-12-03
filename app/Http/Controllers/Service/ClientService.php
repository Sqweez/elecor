<?php


namespace App\Http\Controllers\Service;

use App\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientService {

    public static function getClients(Request $request) {
        $all_client_types = !$request->has('client_types');
        $client_types = explode(',', $request->get('client_types', ''));

        $clientQuery = Client::query()->with(['type', 'connections']);

        $clientQuery->when(!$all_client_types, function ($query) use ($client_types) {
            return $query->whereIn('client_type', $client_types);
        });

        $clientQuery->when($request->has('gender'), function ($query) {
            return $query->where('gender', \request('gender'));
        });

        $clientQuery->when($request->has('lang'), function ($query) {
            return $query->where('lang', \request('lang'));
        });

        $clientQuery->when($request->has('service'), function ($query) use ($request) {
            return $query->with(['connections' => function ($q) use ($request) {
                $q->when($request->has('price'), function ($query) {
                    return $query->where('price', \request('price'));
                });

                $q->when($request->has('is_active'), function ($query) {
                    return $query->where('is_active', !!\request('is_active'));
                });

                $q->when($request->has('company'), function ($query) {
                    return $query->whereIn('company_id', explode(',', \request('company')));
                });

                $q->when($request->has('startDate'), function ($query) {
                    return $query->whereDate('created_at', '>=', Carbon::parse(\request('startDate'))->toDateString());
                });

                $q->when($request->has('finishDate'), function ($query) {
                    return $query->whereDate('created_at', '<=', Carbon::parse(\request('finishDate'))->toDateString());
                });

                $q->when($request->has('is_debtor'), function ($query) {
                    $query->with('transactions');
                    /*$query->withCount(['transactions as debt' => function ($q) {
                        return $q->select(DB::raw("SUM(balance_change) as balance"));
                    }]);

                    $query->whereHas('transactions', function ($q) {
                        $expression = intval(\request('is_debtor')) === 1 ? '<' : '>=';
                        return $q->having(DB::raw("SUM(balance_change)"), $expression, 0);
                    });*/
                });

                $q->where('service_id', $request->get('service'));

                $q->where('is_deleted', 0);
            }]);
        });

        $clients = $clientQuery->get();
        if ($request->has('is_debtor')) {
            $clients = $clients->map(function ($client) {
                $connections = collect($client->connections)->map(function ($connection) {
                    $balance = collect($connection->transactions)->reduce(function ($a, $c) {
                        return $a + intval($c->balance_change);
                    }, 0);

                    unset($connection->balance);
                    unset($connection->transactions);
                    $connection->balance = $balance;
                    return $connection;
                })->filter(function ($connection) {
                    return intval(\request('is_debtor')) === 1 ? $connection->balance < 0 : $connection->balance >= 0;
                });
                unset($client->connections);
                $client->connections = $connections;
                return $client;
            });
        }
        return $clients->filter(function ($client) {
            return count($client->connections);
        })->values();
    }
}
