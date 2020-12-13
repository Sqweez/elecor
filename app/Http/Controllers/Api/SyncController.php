<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function getUrl() {
        $appUpdated = env('APP_UPDATED', false);
        return !$appUpdated ? 'https://elecor.ariesdev.kz/api/' : 'https://admin.elecor.kz/api/';
    }

    public function syncDb() {
        //
    }
}
