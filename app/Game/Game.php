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
    private static $currentRoundId = 0;
    private static $currentRound;
    private static $currentDistributor;
    private static $first_word_player;
    private const DEFAULT_BET = 50;

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
            self::$currentRoundId += 1;
            $round = new Round(self::$currentRoundId);
            self::$currentRound = $round;
            self::$rounds[self::$currentRoundId] = $round;
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

    public static function getCurrentRoundId()
    {
        return self::$currentRoundId;
    }

    public static function getCurrentRound()
    {
        return self::$currentRound;
    }

    public static function getRounds()
    {
        return self::$rounds;
    }
    
    public static function getCurrentDistributor()
    {
        return self::$currentDistributor;
    }

    public static function setCurrentDistributor(Player $distributor)
    {
        self::$currentDistributor = $distributor;
    }

    public static function getCurrentFirstWordPlayer()
    {
        return self::$first_word_player;
    }

    public static function setCurrentFirstWordPlayer(Player $first_word_player)
    {
        self::$first_word_player = $first_word_player;
    }

    public static function getDefaultBet()
    {
        return self::DEFAULT_BET;
    }

    public static function currentRoundMakeBet(int $bet, string $betMaker, Player $player)
    {
        self::$currentRound->makeBet($player, $bet);
        self::$currentRound->addBetToCashBox($bet, $betMaker);
    }

    public static function endRoundWithoutShowingUp()
    {
        self::$currentRound->endRoundWithoutShowingUp();
    }

    public static function getBalanceOfAllPlayers()
    {
        foreach (self::$players as $player) {
            $balanceOfAllPlayers[$player->getName()] = $player->getBalance();
        }

        return $balanceOfAllPlayers;
    }

    public static function endCurrentRound()
    {
        self::$connectAbility = true;
        self::$allPlayersReady = false;

        foreach (self::$players as $player) {
            $player->changeRadinessAfterEndingRound();
            $player->dropCards();
        }
    }

    public static function getAllPlayersIdsNormalizedForGame()
    {
        foreach (self::$players as $player) {
            $normalizedPlayers[] = [
                'id' => $player->getid(),
                'balance' => $player->getBalance()
            ];
        }
        return $normalizedPlayers;
    }
}