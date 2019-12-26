<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index() {
        return Auth::check() ? '123' : 'false';
    }

    public function store(Request $request)
    {
        $user = $request->all();
        $user['token'] = Str::random(60);
        $user['password'] = Hash::make($user['password']);

        return User::create($user);
    }

    public function auth(Request $request) {
        if ($request->exists('token')) {
            $user = User::where('token', $request->get('token'))->get()->first();
            if (isset($user['id'])) {
                // Auth::loginUsingId($user['id']);
                return $user;
            } else {
                return ['error' => 'Данные авторизации устарели'];
            }
        }
        $login = $request->get('login');
        $password = $request->get('password');
        $user = User::where('login', $login)->get()->first();
        if (!isset($user['id'])) {
            return ['error' => 'Пользователь не найден'];
        }

        if (!Hash::check($password, $user['password'])) {
            return ['error' => 'Неверный пароль'];
        }

        // Auth::loginUsingId($user['id']);
        return $user;
    }

    public function checkToken(Request $request) {

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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
