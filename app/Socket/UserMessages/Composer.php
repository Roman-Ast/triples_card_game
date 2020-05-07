<?php

namespace App\Socket\UserMessages;

use App\Socket\UserMessages\CheckConnection;
use App\Socket\UserMessages\RoundStart;
use App\Socket\UserMessages\Reconnect;
use Ratchet\ConnectionInterface;
use App\GameRequisits\Users\Player;

class Composer
{
    public static function checkConnection(array $player_data, ConnectionInterface $player_sender)
    {
        return CheckConnection::check($player_data, $player_sender);
    }

    public static function readyToPlay(array $player_data, Player $player_sender)
    {
        return RoundStart::ready($player_data, $player_sender);
    }

    public static function tryReconnect(array $player_data, ConnectionInterface $player_sender)
    {
        return Reconnect::check($player_data, $player_sender);
    }
}