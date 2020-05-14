<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Session;
use App\Admin;

class GameController extends Controller
{
    public function index($id)
    {
        /*if (!Session::has('message')) {
            return redirect()->route('login');
        }*/
        $user = User::where('id', $id)->first();

        if ($user->admin) {
            return view("admin/commonField", ['user' => $user]);
        }

        return view("game", ['user' => $user]);
    }

    public function admin_panel()
    {
        $user = User::where('admin', true)->first();
        $allUsers = User::where('admin', false)->get();
    
        return view("admin/commonField", ['user' => $user, 'allUsers' => $allUsers]);
    }

    public function chargeBalance(Request $request)
    {
        
        $player = User::find($request->id);
        $player->balance += $request->newBalance;
        $newBalance = $player->balance;
        $playerName = $player->name;
        $player->save();

        return json_encode(['playerName' => $playerName, 'newBalance' => $newBalance]);
    }

    public function runServer()
    {
        $admin = new Admin();
        $this->admin = $admin;
        $state = $this->admin->runServer();

        return $state;
    }

    public function stopServer()
    {
        $state = $this->admin->stopServer();

        return $state;
    }
}