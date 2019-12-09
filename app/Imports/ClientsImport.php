<?php

namespace App\Imports;
use App\Client;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;


class ClientsImport implements ToModel {

    use Importable;

    public function model(array $row)
    {
        return new Client([
            'name' => $row[0],
        ]);
    }
}
