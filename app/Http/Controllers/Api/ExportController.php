<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\Connection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\ExcelController;
use App\Http\Resources\ClientsResource;
use App\Http\Resources\DebtResource;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExportController extends Controller {

    public function exportClients(Request $request) {
        $exportVariant = intval($request->get('variant')) ?? 1;

        $clients = [];

        switch ($exportVariant) {
            case 1:
                $clients = ClientsResource::collection(Client::with('type')->get());
                break;
            case 2:
                $clients = ClientsResource::collection(Client::find(Connection::where('service_id', 5)->where('is_deleted', 0)->get()->pluck('client_id')));;
                break;
            case 3:
                $debts = DebtResource::collection(Client::find(Connection::where('service_id', 5)->where('is_deleted', 0)->get()->pluck('client_id')));
                $debts = collect($debts)->filter(function($i) {
                    return $i !== null;
                })->values()->pluck('id');
                $all_clients_with_mtk = ClientsResource::collection(Client::find(Connection::where('service_id', 5)->where('is_deleted', 0)->get()->pluck('client_id')));
                $clients = $all_clients_with_mtk->filter(function ($i) use ($debts) {
                    return !$debts->contains($i['id']);
                })->values()->all();
                break;
            case 4:
                $debts = DebtResource::collection(Client::find(Connection::where('service_id', 5)->where('is_deleted', 0)->get()->pluck('client_id')));
                $debts = collect($debts)->filter(function($i) {
                    return $i !== null;
                })->values()->pluck('id');
                $all_clients_with_mtk = ClientsResource::collection(Client::find(Connection::where('service_id', 5)->where('is_deleted', 0)->where('price', 2000)->where('is_active', 1)->get()->pluck('client_id')));
                $clients =
                    $all_clients_with_mtk->filter(function ($i) use ($debts) {
                    return !$debts->contains($i['id']);
                })->values()->all();
                break;
            default:
                break;
        }

        $json_data = json_encode($clients);

        $excelService = new ExcelController();
        return $excelService->exportClients($request, $json_data);
    }

    public function exportDebts(Request $request) {
        $debts = DebtResource::collection(Client::all());
        $debts = collect($debts)->filter(function($i) {
            return $i !== null;
        })->values();
        $excelService = new ExcelController();
        return $excelService->exportDebts(json_encode($debts), $request->get('date') ?? Carbon::today()->toDateString());
    }
}
