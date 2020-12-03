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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
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
                if ($connection) {
                    array_push($clients,
                        [
                            'key' => $key,
                            'account' => $account,
                            'balance' => $balance,
                            'client' => $connection['client'],
                            'id' => $connection['id']
                        ]
                    );
                }

            }
        }
        return $clients;
    }

    public function exportClients(Request $request, $clients) {
        $title = "Список клиентов от " . now()->format('d.m.Y');

        $spreadSheet = IOFactory::load(storage_path('app/public/clients_template.xlsx'));
        $sheet = $spreadSheet->getActiveSheet();
        $sheet->setCellValue('A1', $title);

        collect($clients)->each(function ($item, $key) use ($sheet) {
            $initialIndex = 3;
            $index = $initialIndex + $key;
           // dd($item['personal_accounts']);
            $sheet->setCellValue('A' . $index, $item['name']);
            $sheet->setCellValue('B' . $index, join("\n", $item['personal_accounts']->values()->all()));
            $sheet->getStyle('B' . $index)->getAlignment()->setWrapText(true);
            $sheet->setCellValue('C' . $index, join("\n", $item['trademarks']->values()->all()));
            $sheet->getStyle('C' . $index)->getAlignment()->setWrapText(true);
            $sheet->setCellValue('D' . $index, join("\n", $item['addresses']->values()->all()));
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
        $account = Connection::where('personal_account', $account)->where('is_deleted', false)->with('client')->first();
        return $account;
    }

	public function exportDebts($debts) {
        $spreadSheet = IOFactory::load(storage_path('app/public/debt_template.xlsx'));
        $sheet = $spreadSheet->getActiveSheet();
        $sheet->setCellValue('A1', "Выгрузка по дебиторской задолженности");
        collect($debts)->map(function ($item, $key) use ($sheet) {
            $initialIndex = 3;
            $index = $initialIndex + $key;
            $connections = collect($item['connections']);
            $sheet->setCellValue('A' . $index, $item['name']);
            $sheet->setCellValue('B' . $index, join("\n", $connections->pluck('personal_account')->values()->all()));
            $sheet->getStyle('B' . $index)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('B' . $index)->getAlignment()->setWrapText(true);
            $sheet->setCellValue('C' . $index, join("\n", $connections->pluck('trademark')->values()->all()));
            $sheet->getStyle('C' . $index)->getAlignment()->setWrapText(true);
            $sheet->getStyle('C' . $index)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->setCellValue('D' . $index, join("\n", $connections->pluck('address')->values()->all()));
            $sheet->getStyle('D' . $index)->getAlignment()->setWrapText(true);
            $sheet->getStyle('D' . $index)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->setCellValue('E' . $index, join("\n", $connections->pluck('debt')->values()->all()));
            $sheet->getStyle('E' . $index)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('E' . $index)->getAlignment()->setWrapText(true);
            $sheet->setCellValue('F' . $index, join("\n", $item['phones']->values()->all()));
        });

        $writer = new Xlsx($spreadSheet);

        ob_start();
        $writer->save('php://output');
        $content = ob_get_contents();
        ob_end_clean();

        $fileName = Carbon::now()->toDateString() . '_' . Str::random(10) . '_debts.xlsx';
        $file = 'public/excel/debts/' . $fileName;
        Storage::put($file, $content);
        return url('/') . Storage::url($file);

    }

	private function addBalance($connection, $sum) {
        Transaction::create(['connection_id' => $connection, 'balance_change' => $sum, 'user_id' => 0]);
    }
}
