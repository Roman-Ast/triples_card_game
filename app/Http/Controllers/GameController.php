<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Session;
use App\Admin;

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
        $outputArr = [];
        $output = exec('cd .. && php artisan chat_server:serve', $outputArr, $ret_var);
        
        return json_encode(['output' => $outputArr, 'status' => $ret_var]);
    }

    public function stopServer()
    {
        $outputArr = [];
        exec('lsof -i -P -n | grep :8050', $outputArr, $ret_var);
        
        $arr = explode(' ', $outputArr[0]);
        $trimedArr = array_filter($arr, function($item) {
            if ($item !== '') {
                return $item;
            }
        });
        $slicedArr = array_slice($trimedArr, 0);
        $processPID = $slicedArr[1];

        $killedOutputArr = [];

        exec('kill -9 ' . $processPID, $killedOutputArr, $killed_ret_var);
        return json_encode(['outputGrep' => $outputArr,'outputKilled' => $killedOutputArr, 'status' => $killed_ret_var]);
    }
}