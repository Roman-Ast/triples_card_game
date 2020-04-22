<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class RegisterController extends Controller
{
    public function index()
    {
        return view("auth/register");
    }
    
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|unique:App\User,name',
            'email' => 'required|email:rfc,dns|unique:App\User,email',
            'password' => 'required|min:7',
            'password_confirmation' => 'same:password'
        ]);

        $user = new User([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password
        ]);
        $user->save();

        return view("/game", ['user' => $user]);
    }
}