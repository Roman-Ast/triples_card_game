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
    private static $isCooking = false;
    private static $currentRoundId = 0;
    private static $currentRound;
    private static $currentDistributor;
    private static $first_word_player;
    private static $lastRoundWinner;
    private static $lastRoundCashBox;
    private static $noneNotWinnersAgreedToCook = false;
    private static $someNotWinnerAgreedToCook = false;
    private static $allwinnersAgreedToCook = false;
    private static $allPlayersWinners = false;
    private static $cookingPlayers = [];
    private const DEFAULT_BET = 50;
    private const STEP_IN_BETS = 10;
    private const TAX = 10;

    public static function isCooking()
    {
        return self::$isCooking;
    }

    public static function informAboutCooking(Player $informingPlayer, bool $readiness)
    {
        if (count(self::$cookingPlayers) < 1) {
            foreach (self::$players as $player) {
                self::$cookingPlayers[] = [
                    'id' => $player->getId(),
                    'name' => $player->getName(),
                    'player' => $player,
                    'readyForCook' => null,
                    'winnerOfLastRound' => in_array($player->getName(), self::getCurrentRound()->getWinnerAfterOpeningCards()) ? true : false
                ];
            }
            foreach (self::$cookingPlayers as $index => $item) {
                if ($item['player']->getId() == $informingPlayer->getId()) {
                    self::$cookingPlayers[$index]['readyForCook'] = $readiness;
                }
            }
        } else {
            foreach (self::$cookingPlayers as $index => $item) {
                if ($item['id'] == $informingPlayer->getId()) {
                    self::$cookingPlayers[$index]['readyForCook'] = $readiness;
                }
            }
        }
        //если у всех одинаковое кол-во очков и все сказали и все согласны варить
        if (count(self::$players) === count(self::$lastRoundWinner)){
            self::$allPlayersWinners = true;
        }
        //если все непобедители высказались и кто-то из них согласился на свару
        if (self::allNotWinnersSaid() && self::someNotWinnersAgreeToCook()) {
            self::$isCooking = true;
            self::$someNotWinnerAgreedToCook = true;
        } 
        //если все непобедители высказались и все отказались от свары то $someNotWinnerAgreedToCook останется false
        elseif (self::allNotWinnersSaid() && !self::someNotWinnersAgreeToCook() && !self::$allPlayersWinners && !self::allWinnersSaid()) {
            self::$noneNotWinnersAgreedToCook = true;
        }
        //если все непобедители отказались и все победители согласились на свару
        elseif (self::allWinnersSaid() && self::allWinnersAgreeToCook()) {
            self::$isCooking = true;
            self::$allwinnersAgreedToCook = true;
        }
    }

    public static function allNotWinnersSaid()
    {
        foreach (self::$cookingPlayers as $item) {
            if (!$item['winnerOfLastRound'] && $item['readyForCook'] === null) {
                return false;
            }
        }

        return true;
    }
    public static function someNotWinnersAgreeToCook()
    {
        foreach (self::$cookingPlayers as $item) {
            if ($item['readyForCook'] && $item['winnerOfLastRound'] === false) {
                return true;
            }
        }

        return false;
    }

    public static function allWinnersSaid()
    {
        foreach (self::$cookingPlayers as $item) {
            if ($item['winnerOfLastRound']) {
                if ($item['readyForCook'] === null) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function allWinnersAgreeToCook()
    {
        foreach (self::$cookingPlayers as $item) {
            if ($item['winnerOfLastRound']) {
                if ($item['readyForCook'] === false) {
                    return false;
                }
            }
        }

        return true;
    }



    public static function isNoneNotWinnersAgreedToCook()
    {
        return self::$noneNotWinnersAgreedToCook;
    }

    public static function isAllWinnersAgreedToCook()
    {
        return self::$allwinnersAgreedToCook;
    }

    public static function isSomeNotWinnerAgreedToCook()
    {
        return self::$someNotWinnerAgreedToCook;
    }

    public static function setLastRoundCashBox(int $cashBox)
    {
        self::$lastRoundCashBox = $cashBox;
    }

    public static function getLastRoundCashBox()
    {
        return self::$lastRoundCashBox;
    }

    public static function setLastRoundWinner(array $winner)
    {
        self::$lastRoundWinner = $winner;
    }

    public static function getLastRoundWinner()
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
            self::$currentRound->setStatus('round');
            self::$currentRound->setCashBox(0);
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
        foreach (self::getAllPlayers() as $player) {
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
        foreach (self::getAllPlayers() as $player) {
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