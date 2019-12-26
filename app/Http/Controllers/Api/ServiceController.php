<?php

namespace App\Http\Controllers\api;

use App\Service;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Service[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Service::all();
    }


    public function store(Request $request)
    {
        $service = $request->all();
        return Service::create($service);
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->all();
        $service->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $service = Service::find($id);
        $service->oneTimeService()->delete();
        $service->delete();
    }


    public function getTempServices(Service $service) {
        return $service->oneTimeService;
    }
}
