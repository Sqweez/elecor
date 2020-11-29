<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class VueController extends Controller
{
    public function index() {
        return view('index');
    }
}
