<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Session;

class GameController extends Controller
{
    public function index($id)
    {
        $user = User::where('id', $id)->first();
        return view("game", ['user' => $user]);
    }
}