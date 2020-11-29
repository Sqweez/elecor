<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index() {
        return UserResource::collection(User::with(['user_role'])->get());
    }

    public function store(Request $request)
    {
        $user = $request->all();
        $user['token'] = Str::random(60);
        $user['password'] = Hash::make($user['password']);

        $user = User::create($user);

        return new UserResource($user);
    }

    public function auth(Request $request) {
        if ($request->exists('token')) {
            $user = User::where('token', $request->get('token'))->get()->first();
            if (isset($user['id'])) {
                return new UserResource($user);
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

        return new UserResource($user);
    }

    public function checkToken(Request $request) {

    }

    public function getRoles() {
        return Role::all();
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
     * @param \Illuminate\Http\Request $request
     * @param User $user
     * @return UserResource
     */
    public function update(Request $request, User $user)
    {
        $data = $request->all();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
    }
}
