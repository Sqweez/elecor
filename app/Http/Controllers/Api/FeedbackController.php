<?php

namespace App\Http\Controllers\Api;

use App\Feedback;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeedbackResource;
use Illuminate\Http\Request;
use Illuminate\Queue\RedisQueue;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Feedback[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return FeedbackResource::collection(Feedback::all()->sortByDesc('created_at'));
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
