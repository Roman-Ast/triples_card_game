<?php

namespace App\Game;

use App\Game\Round;
use App\Game\Users\Player;

class Game
{
    private static $players = [];
    private static $rounds = [];
    private static $connectAbility = true;
    private static $allPlayersReady = false;
    private static $roundCount = 1;

    public static function addPlayer(Player $player)
    {
        self::$players[] = $player;
    }

    public static function areAllPlayersReady()
    {
        return self::$allPlayersReady;
    }
    public static function getAllPlayers()
    {
        return self::$players;
    }

    public static function SayReadyToGame()
    {
        if (self::checkPlayersReady()) {       
            self::$connectAbility = false;
            $round = new Round();
            self::$rounds[] = $round;
            self::$allPlayersReady = true;
            $round->start();
        }
    }

    private static function checkPlayersReady()
    {
        foreach (self::$players as $player) {
            if (!$player->getRadiness()) {
                return false;
            }
        }
        return true;
    }

    public static function checkConnectAbility()
    {
        return self::$connectAbility;
    }
}