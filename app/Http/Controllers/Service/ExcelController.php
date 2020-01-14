<?php

namespace App\Http\Controllers\Service;

use App\Connection;
use App\Http\Controllers\Api\ConnectionController;
use App\Http\Controllers\Controller;
use App\Transaction;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelController extends Controller {
    public function parseClients(Request $request) {
        $clients = [];
        $spreadsheet = $this->loadFile($request);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->getRowIterator();
        foreach ($rows as $key => $row) {
            $nameCell = $sheet->getCell('E' . ($key));
            $phoneCell = $sheet->getCell('F' . ($key));
            $name = $nameCell->getValue();
            $phone = $phoneCell->getValue();
            if ($name) {
                array_push($clients, ['name' => $name, 'phone' => $phone,]);
            }
        }
        return $clients;
    }

    public function parseBalance(Request $request) {
        $clients = [];
        $notFounds = [];
        $spreadsheet = $this->loadFile($request);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->getRowIterator();
        foreach ($rows as $key => $row) {
            $accountCell = $sheet->getCell('A' . ($key));
            $balanceCell = $sheet->getCell('B' . ($key));
            $account = trim($accountCell->getValue());
            $balance = $balanceCell->getValue();
            if (intval($account) !== 0) {
                $connection = $this->findAccount(preg_replace('/\s+/', '', $account));
                array_push($clients, ['key' => $key, 'account' => $account, 'balance' => $balance, 'client' => $connection['client'], 'id' => $connection['id']]);
            }
        }
        return $clients;
    }

    private function loadFile(Request $request) {
        $path = 'app/public/' . $request->get('file');
        $file = storage_path($path);
        return IOFactory::load($file);
    }

    private function findAccount($account) {
        $account = Connection::where('personal_account', $account)->with('client')->first();
        return $account;
    }

    private function addBalance($connection, $sum) {
        Transaction::create(['connection_id' => $connection, 'balance_change' => $sum, 'user_id' => 0]);
    }
}
