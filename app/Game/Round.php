<?php

namespace App\Game;

use App\Game\Cards\Diller;
use App\Game\Game;
use App\Game\Users\Player;

class Round
{
    private $id;
    private const DEFAULT_BET = 50;
    private $summOfDefaultBets = [];
    private $distributor;
    private $first_word_right;
    private $cashBox;
    private $totalCashBoxSumm;
    private $currentStepPlayer;
    private $nextStepPlayer;
    private $roundParameters = [];
    private $lastBet = 0;
    private $playerOpenCardAbility;
    private $playerTakingConWithoutShowingUp;
    private $winner;

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
            $this->summOfDefaultBets[] = [
                "betMaker" => $player->getName(),
                "defaultBet" => $player->makeDefaultBet(self::DEFAULT_BET)
            ];
        }

        foreach ($this->summOfDefaultBets as $item) {
            $this->totalCashBoxSumm += $item['defaultBet'];
        }
    }

    public function getRoundDefaultBets()
    {
        return $this->summOfDefaultBets;
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
        $this->totalCashBoxSumm += $bet;
        var_dump($this->totalCashBoxSumm);
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
            foreach ($this->roundParameters as $index => $player) {
                if ($playerSteping->getConnResourceId() == $player['resourceId']) {
                    $this->roundParameters[$index]['state'] = 'raise';
                }
            }
        } else if ($bet === $this->lastBet) {
            foreach ($this->roundParameters as $index => $player) {
                if ($playerSteping->getConnResourceId() == $player['resourceId']) {
                    $this->roundParameters[$index]['state'] = 'collate';
                }
            }
        } else {
            foreach ($this->roundParameters as $index => $player) {
                if ($playerSteping->getConnResourceId() == $player['resourceId']) {
                    $this->roundParameters[$index]['state'] = 'save';
                }
            }
        }
        if ($bet > 0) {
            $this->lastBet = $bet;
        }
        
        $playerOpenCardAbility = $this->checkRoundParameters();

        if ($playerOpenCardAbility) {
            foreach (Game::getAllPlayers() as $key => $player) {
                if ($player->getConnResourceId() == $playerOpenCardAbility) {
                    $this->playerOpenCardAbility = $player;
                }
            }
        }
    }

    private function checkRoundParameters()
    {
        //проверяем, все ли сходили
        foreach ($this->roundParameters as $index => $player) {
            if (!$player['state']) {
                return;
            }
        }
        //если да, то проверяем пора ли завершать раунд
        $playerOpenCardAbility = null;
        $countOfRaises = 0;
        $countOfCollates = 0;

        foreach ($this->roundParameters as $index => $player) {
            if ($player['state'] === 'raise') {
                $countOfRaises += 1;
            }
        }

        foreach ($this->roundParameters as $index => $player) {
            if ($player['state'] === 'collate') {
                $countOfCollates += 1;
            }
        }

        if ($countOfRaises < 2 && $countOfCollates > 0) {
            foreach ($this->roundParameters as $index => $player) {
                if ($player['state'] === 'raise') {
                    if (isset($this->roundParameters[$index - 1])) {
                        if ($this->roundParameters[$index - 1]['state'] === 'save') {
                            $playerOpenCardAbility = $player['resourceId'];
                        } else if ($this->roundParameters[$index - 1]['state'] === 'collate') {
                            $playerOpenCardAbility = $this->roundParameters[$index - 1]['resourceId'];
                        }
                    } else {
                        if ($this->roundParameters[count($this->roundParameters) - 1]['state'] === 'save') {
                            $playerOpenCardAbility = $player['resourceId'];
                        } else if ($this->roundParameters[count($this->roundParameters) - 1]['state'] === 'collate') {
                            $playerOpenCardAbility = $this->roundParameters[count($this->roundParameters) - 1]['resourceId'];
                        }
                    }
                }
            }
        } else if ($countOfRaises < 2 && $countOfCollates === 0) {
            foreach ($this->roundParameters as $index => $player) {
                if ($player['state'] === 'raise') {
                    $this->playerTakingConWithoutShowingUp = $player['resourceId'];
                }
            }
        }

        return $playerOpenCardAbility;
    }

    private function setInitRoundParameters()
    {
        $players = Game::getAllPlayers();

        foreach ($players as $index => $player) {
            $this->roundParameters[] = [
                'resourceId' => $player->getConnResourceId(),
                'state' => null
            ];
        }
    }

    public function getCurrentStepPlayer()
    {
        return $this->currentStepPlayer;
    }

    public function getNextStepPlayer()
    {
        return $this->nextStepPlayer;
    }

    public function getLastBet()
    {
        return $this->lastBet;
    }

    public function getPlayerTakingConWithoutShowingUp()
    {
        if ($this->playerTakingConWithoutShowingUp) {
            foreach (Game::getAllPlayers() as $player) {
                if ($player->getConnResourceId() === $this->playerTakingConWithoutShowingUp) {
                    return $player->getName();
                }
            }
        }
        return false;
    }
}