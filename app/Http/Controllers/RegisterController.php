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
            'email' => 'required|email:rfc,dns|unique:App\User,email'
        ]);

        $user = new User([
            "name" => $request->name,
            "email" => $request->email
        ]);

        $user->save();

        return redirect()->route('login');
    }
}