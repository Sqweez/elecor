<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\MailingTemplate;
use Illuminate\Http\Request;

class MailingTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return MailingTemplate::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $template = $request->all();
        return MailingTemplate::create($template);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param MailingTemplate $template
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        MailingTemplate::find($id)->update($request->all());
        return MailingTemplate::find($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        MailingTemplate::find($id)->delete();
    }
}
