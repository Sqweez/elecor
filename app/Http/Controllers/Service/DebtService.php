<?php


namespace App\Http\Controllers\Service;


use App\Client;
use App\Connection;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtService {
    public static function getDebts(Request $request) {

        $services = explode(',', $request->get('services', ""));
        $is_all_services = !$request->has('services') || count($services) === 0;


        $clientsQuery = Client::query();


        $clientsQuery->with(['connections' => function ($query) use ($request) {
            $startDate = \request('startDate', '1970-01-01');
            $finishDate = \request('finishDate', now());
            $startDate = Carbon::parse($startDate)->toDateString();
            $finishDate = Carbon::parse($finishDate)->toDateString();
            $query->withCount(['transactions as balance' => function ($q) use ($startDate, $finishDate) {
                return $q->select(DB::raw("SUM(balance_change) as balance"))
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $finishDate);
            }]);

            $query->whereHas('transactions', function ($q) use ($startDate, $finishDate) {
                return $q->having(DB::raw("SUM(balance_change)"), '<', 0)
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $finishDate);
            });

            $query->where('is_deleted', 0);

            $services = explode(',', $request->get('services', ""));
            $is_all_services = !$request->has('services') || count($services) === 0;

            $query->when(!$is_all_services, function ($q) use ($services) {
                $q->whereIn('service_id', $services);
            });
        }]);

        $clientsQuery->with(['phones']);


        return $clientsQuery->get()->filter(function ($client) {
            return count($client->connections) > 0;
        })->values();
    }

    public static function getDebtsOld(Request $request) {

        $services = explode(',', $request->get('services', ""));
        $is_all_services = !$request->has('services') || count($services) === 0;

        $startDate = \request('startDate', '1970-01-01');
        $finishDate = \request('finishDate', now());
        $startDate = Carbon::parse($startDate)->toDateString();
        $finishDate = Carbon::parse($finishDate)->toDateString();

        $clientsQuery = Client::query();


        $clientsQuery->with(['connections.transactions' => function ($q) use ($startDate, $finishDate) {
            $q->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $finishDate);
        }]);


        $clientsQuery->with(['connections' => function ($query) use ($request) {
            $query->where('is_deleted', 0);

            $services = explode(',', $request->get('services', ""));
            $is_all_services = !$request->has('services') || count($services) === 0;

            $query->when(!$is_all_services, function ($q) use ($services) {
                $q->whereIn('service_id', $services);
            });
        }]);

        $clientsQuery->with(['phones']);


        return $clientsQuery->get()
            ->map(function ($client) {
                $connections = collect($client->connections)
                    ->map(function ($connection) {
                        $balance = collect($connection->transactions)->reduce(function ($a, $c) {
                            return $a + intval($c->balance_change);
                        }, 0);

                        unset($connection->balance);
                        unset($connection->transactions);
                        $connection->balance = $balance;
                        return $connection;
                    })->filter(function ($connection) {
                        return $connection->balance < 0;
                    });
                unset($client->connections);
                $client->connections = $connections;
                return $client;
            })->filter(function ($client) {
                return count($client->connections) > 0;
            })->values();
    }
}
