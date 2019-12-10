<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileStocksResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\StockResource;
use App\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $isMobile = $request->get('mobile');
        $stocks = null;
        if ($isMobile) {
            $stocks = MobileStocksResource::collection(Stock::all()->where('is_visible', true));
        } else {
            $stocks = StockResource::collection(Stock::all());
        }
        return $stocks;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        return new StockResource(Stock::create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Stock $stock
     * @param \Illuminate\Http\Request $request
     * @return StockResource
     */
    public function update(Stock $stock, Request $request)
    {
        $data = $request->all();
        $stock->update($data);
        return new StockResource($stock);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $stock = Stock::find($id);
        $this->deletePhoto($stock['image']);
        $stock->delete();
    }

    private function deletePhoto($url) {
        if ($url === 'uploads/no_client_photo.jpg') {
            return null;
        }
        Storage::disk('public')->delete($url);
    }
}
