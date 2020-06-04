<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Session;
use App\Admin;
use App\Game as modelGame;
use App\GameRequisits\Game;
use DB;
use App\Tax;

class GameController extends Controller
{
    private $process;

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

    public function runServer()
    {
        $response = Admin::runServer();

        return json_encode($response);
    }

    public function chargeBalanceOutGame(Request $request)
    {
        $player = User::find($request->playerId);
        if ($player) {
            $player->balance += $request->newBalance;
            $player->save();

            return json_encode(['name' => $player->name, 'balance' => $player->balance]);
        } else {
            return json_encode(['error' => 'no player was found']);
        }
        
    }
}