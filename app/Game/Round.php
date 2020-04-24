<?php

namespace App\Game;

use App\Game\Cards\Diller;
use App\Game\Game;
use App\Game\Users\Player;

class Round
{
    private $id;
    private const DEFAULT_BET = 50;
    private $summ_of_default_bets = [];
    private $summ_of_all_bets = [];
    private $distributor;
    private $first_word_right;
    private $cashBox;
    private $currentStepPlayer;
    private $nextStepPlayer;
    private $roundParameters = [];
    private $lastBet = 0;
    private $playerOpenCardAbility;

    public function __construct(int $currentRound)
    {
        $this->id = $currentRound;
    }
    public function start()
    {
        Diller::distribute();
        $this->takeDefaultBet();
        $this->setDistributor();
        $this->setFirstWordPlayer();
        $this->setInitRoundParameters();
    }

    private function takeDefaultBet()
    {
        foreach (Game::getAllPlayers() as $player) {
            $this->summ_of_default_bets[] = [
                "betMaker" => $player->getName(),
                "defaultBet" => $player->makeDefaultBet(self::DEFAULT_BET)
            ];
        }
    }

    public function getRoundDefaultBets()
    {
        return $this->summ_of_default_bets;
    }

    public function setDistributor()
    {
        if ($this->id == 1) {
            $players = Game::getAllPlayers();
            $rand = rand(0, count($players) - 1);
            $this->distributor = $players[$rand];
            
            Game::setCurrentDistributor($this->distributor);
        } else {
            foreach ($players as $key => $player) {
                if ($player->getId() > $this->distributor->getId()) {
                    $this->distributor = $player;
                    Game::setCurrentDistributor($this->distributor);
                } else {
                    $this->distributor = $players[0];
                    Game::setCurrentDistributor($this->distributor);
                }
            }
        }
    }

    public function setFirstWordPlayer()
    {
        $players = Game::getAllPlayers();

        foreach ($players as $index => $player) {
            if ($player == $this->distributor) {
                $currentDistributorIndex = $index;
            }
        }

        if (isset($players[$currentDistributorIndex + 1])) {
            $this->first_word_right = $players[$currentDistributorIndex + 1];
        } else {
            $this->first_word_right = $players[0];
        }

        Game::setCurrentFirstWordPlayer($this->first_word_right);
    }

    public function addBetToCashBox(int $bet, string $betMaker)
    {
        $this->cashBox[$betMaker] = $bet;
    }

    public function getRoundCashBox()
    {
        return $this->cashBox;
    }

    public function getPlayerOpenCardAbility()
    {
        if ($this->playerOpenCardAbility) {
            return $this->playerOpenCardAbility->getName();
        }
        return false;
        
    }
    public function makeBet(Player $playerSteping, int $bet)
    {
        //игрок который ходит сейчас
        $players = Game::getAllPLayers();
        $this->currentStepPlayer = $playerSteping;

        foreach ($players as $index => $player) {
            if ($player == $this->currentStepPlayer) {
                $currentStepPlayerIndex = $index;
            }
        }

        //игрок который будет делать следующий ход
        if (isset($players[$currentStepPlayerIndex + 1])) {
            $this->nextStepPlayer = $players[$currentStepPlayerIndex + 1];
        } else {
            $this->nextStepPlayer = $players[0];
        }

        //обновление параметров раунда и текущего состояния
        
        if ($bet > $this->lastBet) {
            foreach ($this->roundParameters as $playerId => $state) {
                if ($playerSteping->getConnResourceId() == $playerId) {
                    $this->roundParameters[$playerId] = 'raise';
                }
            }
        } else if ($bet === $this->lastBet) {
            foreach ($this->roundParameters as $playerId => $state) {
                if ($playerSteping->getConnResourceId() == $playerId) {
                    $this->roundParameters[$playerId] = 'collate';
                }
            }
        } else if ($bet < $this->lastBet) {
            foreach ($this->roundParameters as $playerId => $state) {
                if ($playerSteping->getConnResourceId() == $playerId) {
                    $this->roundParameters[$playerId] = 'save';
                }
            }
        }
        $this->lastBet = $bet;
        //var_dump($this->roundParameters);
        $playerOpenCardAbility = $this->checkRoundParameters();

        if ($playerOpenCardAbility) {
            foreach (Game::getPlayers() as $key => $player) {
                if ($player->getConnResourceId() == $playerOpenCardAbility) {
                    $this->playerOpenCardAbility = $player;
                }
            }
        }
    }

    private function checkRoundParameters()
    {
        //проверяем, все ли сходили
        foreach ($this->roundParameters as $playerConnResourceId => $state) {
            if (!$state) {
                return;
            }
        }
        //если да, то проверяем пора ли завершать раунд
        $playerOpenCardAbility = null;
        
        for ($i = 0; $i < count($this->roundParameters); $i++) { 
            if ()
        }
        return $playerOpenCardAbility;

        /*foreach ($this->roundParameters as $playerConnResourceId => $state) {
            $normalizedForCheckingArr[] = $state;
        }

        foreach ($normalizedForCheckingArr as $index => $state) {
            if ($state == 'raise') {
                if ($normalizedForCheckingArr[$index - 1] === 'save') {
                    $playerWithState = 'raise';
                } else if ($normalizedForCheckingArr[$index - 1] === 'collate') {

                }
            }
        }*/
    }

    private function setInitRoundParameters()
    {
        $players = Game::getAllPlayers();

        foreach ($players as $index => $player) {
            $this->roundParameters[$player->getConnResourceId()] = null;
        }

        /*foreach ($this->roundParameters as $playerId => $state) {
            if ($playerId == $this->distributor->getId()) {
                $this->roundParameters[$player->getId()] = 'distributor';
            }
        }*/
    }

    public function getCurrentStepPlayer()
    {
        return $this->currentStepPlayer;
    }

    public function getNextStepPlayer()
    {
        return $this->nextStepPlayer;
    }
}