<?php

namespace App\GameRequisits;

use Ratchet\ConnectionInterface;
use App\GameRequisits\Round;
use App\GameRequisits\Users\Player;
use App\Game as Game_model;
use App\Tax;
use DB;

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
    private static $lastRoundWinner;
    private const DEFAULT_BET = 50;
    private const STEP_IN_BETS = 10;
    private const TAX = 10;

    public static function setLastRoundWinner(array $winner)
    {
        self::$lastRoundWinner = $winner;
    }

    public static function getLastRoundWinner() :array
    {
        return self::$lastRoundWinner;
    }

    public static function deletePlayerDueToDisconnect(ConnectionInterface $conn):void
    {
        foreach (self::$players as $index => $player) {
            if ($player->getConnResourceId() === $conn->resourceId) {
                unset(self::$players[$index]);
            }
        }
        foreach (self::$players as $index => $player) {
            var_dump($player->getId());
        }
        self::$players = array_slice(self::$players);
    }

    public static function getTax()
    {
        return self::TAX;
    }

    public static function getStepInBets()
    {
        return self::STEP_IN_BETS;
    }

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

    public static function endRoundAfterOpeningCards()
    {
        self::$currentRound->endRoundAfterOpeningCards();
    }

    public static function shareCashBoxAfterOpening()
    {
        self::$currentRound->shareCashBoxAfterOpening();
    }

    public static function getBalanceOfAllPlayers()
    {
        foreach (self::$players as $player) {
            $balanceOfAllPlayers[$player->getName()] = $player->getBalance();
        }

        return $balanceOfAllPlayers;
    }

    public static function getAllPlayersIdsNormalizedForGame()
    {
        foreach (self::$players as $player) {
            $normalizedPlayers[] = [
                'id' => $player->getId(),
                'balance' => $player->getBalance()
            ];
        }
        return $normalizedPlayers;
    }

    public static function getAllPlayersCards()
    {
        foreach (self::$players as $player) {
            if (!in_array($player->getName(), self::getCurrentRound()->getSavingPlayers())) {
                $normalized[] = [
                    'name' => $player->getName(),
                    'cards' => array_map(function($card) {
                        return $card->getFace();
                    }, $player->getCardsOnHand())
                ];
            }
        }
       
        return $normalized;
    }

    public static function getPlayersPointsAfterOpeningCards()
    {
        foreach (self::$players as $player) {
            if (!in_array($player->getName(), self::getCurrentRound()->getSavingPlayers())) {
                $normalizedPoints[] = [
                    'name' => $player->getName(),
                    'points' => $player->getCardsValueAfterOpening()
                ];
            }
        }
        return $normalizedPoints;
    }

    public static function endCurrentRound()
    {
        
        /*$taxSum = (int)self::getCurrentRound()->getTaxSum();
        $roundId = (int)self::getCurrentRound()->getId();
        $game = DB::table('game')->orderBy('id', 'desc')->first();
        $tax = new Tax();
        $tax->game_number = $game->id;
        $tax->round_number = $roundId;
        $tax->sum = $taxSum;

        $tax->save();*/

        self::$connectAbility = true;
        self::$allPlayersReady = false;

        foreach (self::$players as $player) {
            $player->changeRadinessAfterEndingRound();
            $player->dropCards();
        }
    }
}