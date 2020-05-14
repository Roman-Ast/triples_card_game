<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Password;

class LoginController extends Controller
{
    public function index()
    {
        return view("auth/login");
    }
    
    public function login(Request $request)
    {
        $user = User::where('email', '=', $request->email)->first();
        $pass = Password::first();
        //dd($user);
        if ($user) {
            if ($user->admin) {
                if ($user->password == $request->password) {
                    return redirect()->route('admin_panel')->with("message", "authIsValid");
                } else {
                    return back()
                        ->with("message", "Вы ввели неправильные email и/или пароль...")
                        ->with("class", "alert-danger");
                }
            } else {
           
                if (password_verify($request->password, $pass->password)) {
                    return redirect()->route("game", [$user])->with("message", "authIsValid");
                } else {
                    return back()
                        ->with("message", "Вы ввели неправильные email и/или пароль...")
                        ->with("class", "alert-danger");
                }
            }
        } else {
            return back()
                ->with("message", "Вы ввели неправильные email и/или пароль...")
                ->with("class", "alert-danger");
        }
    }
}