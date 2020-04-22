<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class LoginController extends Controller
{
    public function index()
    {
        return view("auth/login");
    }
    
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        
        if ($user && $user->password == $request->password) {
            return redirect()->route("game", [$user])->with("message", "authIsValid");
        } else {
            return back()
                ->with("message", "Вы ввели неправильные email и/или пароль...")
                ->with("class", "alert-danger");
        }
    }
}