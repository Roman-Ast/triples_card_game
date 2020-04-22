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
    private static $currentRound = 0;

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
        //var_dump(self::$players);
        return self::$players;
    }

    public static function SayReadyToGame()
    {
        if (self::checkPlayersReady()) {       
            self::$connectAbility = false;
            self::$currentRound += 1;
            $round = new Round(self::$currentRound);
            self::$rounds[self::$currentRound] = $round;
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

    public static function getAllPlayersNormalizedForGame()
    {
        foreach (self::$players as $player) {
            $normalizedPlayers[] = [
                'name' => $player->getName(),
                'balance' => $player->getBalance()
            ];
        }
        return $normalizedPlayers;
    }

    public static function getCurrentRound()
    {
        return self::$currentRound;
    }

    public static function getRounds()
    {
        return self::$rounds;
    }
}