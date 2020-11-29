<?php

namespace App\Http\Controllers\Api;

use App\Feedback;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeedbackResource;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Queue\RedisQueue;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return FeedbackResource::collection(Feedback::with(['client'])->get()->sortByDesc('created_at'));
    }

    public function count() {
        $count = Feedback::where('is_worked', false)->count() + Order::where('is_worked', false)->count();
        return $count;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $feedback = $request->all();
        return Feedback::create($feedback);
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Feedback $feedback
     * @param \Illuminate\Http\Request $request
     * @return Feedback
     */
    public function update(Feedback $feedback, Request $request)
    {
        $data = $request->all();
        $feedback->update($data);
        return $feedback;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
