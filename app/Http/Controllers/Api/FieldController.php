<?php

namespace App\Http\Controllers\Api;

use App\AdditionalField;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
;

class FieldController extends Controller
{
    public function index() {
        return AdditionalField::all();
    }

    public function create(Request $request) {
        $field = $request->all();
        return AdditionalField::create($field);
    }

    public function change(Request $request, $id) {
        $field = AdditionalField::find($id);
        $field->update($request->all());
    }

    public function destroy($id) {
        $field = AdditionalField::find($id);
        $field->delete();
    }
}
