<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function upload(Request $request) {
        $file = $request->file('file');
        $path = $file->store('uploads', 'public');
        return $path;
    }

    public function delete(Request $request) {
        $file = $request->get('file');
        Storage::disk('public')->delete($file);
        return;
    }
}
