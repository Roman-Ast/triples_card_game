<?php

namespace App;

use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use App\Socket\ChatSocket;
use App\Game as ModelGame;
use App\GameRequisits\Game;

class Admin
{
    private static $process;

    public static function runServer()
    {
        $outputArr = [];
        $output = exec('cd .. && php artisan chat_server:serve', $outputArr, $ret_var);
        
        return [
            'output' => $outputArr,
            'status' => $ret_var
        ];
    }

    public static function makeRecordAboutGame()
    {
        $game = new ModelGame();
        $game->round_qty = count(Game::getRounds());
        $game->cooking_qty = count(Game::getCookings());
        $game->total_cashbox = Game::getTotalCashBoxesSum();
        $game->total_tax = Tax::where('game_id', Game::getId())->sum('sum');
        $game->game_ended_at = date('d F Y H:i');
        $game->save();
    }

    public static function stopServer()
    {
        self::makeRecordAboutGame();

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
        
        return [
            'outputGrep' => $outputArr,
            'outputKilled' => $killedOutputArr,
            'status' => $killed_ret_var
        ];
    }
}
