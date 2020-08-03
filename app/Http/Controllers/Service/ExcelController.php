<?php

namespace App\Http\Controllers\Service;

use App\Client;
use App\Connection;
use App\Http\Controllers\Api\ConnectionController;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientsResource;
use App\Http\Resources\DebtResource;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    public function exportClients(Request $request, $json_data) {
        $exportVariant = intval($request->get('variant')) ?? 1;

        $title = "";

        switch ($exportVariant) {
            case 1: {
                $title = 'Все клиенты';
                break;
            }
            case 2: {
                $title = 'Клиент подключенные к МТК';
                break;
            }
            case 3: {
                $title = 'Клиенты подключенные к МТК без дебиторской задолженности';
                break;
            }
            default:
                break;
        }

        $clients = json_decode($json_data, true);

        $spreadSheet = IOFactory::load(storage_path('app/public/clients_template.xlsx'));
        $sheet = $spreadSheet->getActiveSheet();

        collect($clients)->each(function ($item, $key) use ($sheet) {
            $initialIndex = 3;
            $index = $initialIndex + $key;
            $sheet->setCellValue('A' . $index, $item['name']);
            $sheet->setCellValue('B' . $index, join("\n", $item['personal_accounts']));
            $sheet->getStyle('B' . $index)->getAlignment()->setWrapText(true);
            $sheet->setCellValue('C' . $index, join("\n", $item['trademarks']));
            $sheet->getStyle('C' . $index)->getAlignment()->setWrapText(true);
            $sheet->setCellValue('D' . $index, join("\n", $item['addresses']));
            $sheet->getStyle('D' . $index)->getAlignment()->setWrapText(true);
        });

        $writer = new Xlsx($spreadSheet);

        ob_start();
        $writer->save('php://output');
        $content = ob_get_contents();
        ob_end_clean();

        $fileName = Carbon::now()->toDateString() . '_' . Str::random(10) . '_clients.xlsx';
        $file = 'public/excel/clients/' . $fileName;
        Storage::put($file, $content);
        return url('/') . Storage::url($file);
    }

    private function loadFile(Request $request, $filename = '') {
        $filePath = $request->has('file') ? $request->get('file') : $filename;
        $path = 'app/public/' . $filePath;
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
