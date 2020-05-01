<?php

namespace App\Game;

use App\Game\Cards\Diller;
use App\Game\Game;
use App\Game\Users\Player;
use App\User;

class Round
{
    private $id;
    private const DEFAULT_BET = 50;
    private $defaultBets = [];
    private $sumOfDefaultBets = 0;
    private $bets = [];
    private $cashBox = 0;
    private $distributor;
    private $first_word_right;
    private $currentStepPlayer;
    private $nextStepPlayer;
    private $roundParameters = [];
    private $lastBet = 0;
    private $playerOpenCardAbility;
    private $playerTakingConWithoutShowingUp;
    private $winner;
    private $winnerAfterOpeningCards;
    private $endRoundWithoutShowingUp = false;
    private $playersSaving = [];
    private $playersRaising = [];
    private $lastRasingPlayer;
    private $lastCollatingPlayer;
    private $lastSavingPlayer;
    private $isFirstRoundOfSales = true;

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

    public function getSumOfDeafultBets()
    {
        return $this->sumOfDefaultBets;
    }

    public function getNextStepPlayerToCollate()
    {
        $sumOfbetsOfLastRasingPlayer = 0;
        $sumOfbetsOfNextStepingPlayer = 0;

        foreach ($this->bets as $name => $bet) {
            if ($this->lastRasingPlayer->getName() === $name) {
                $sumOfbetsOfLastRasingPlayer = $bet;
            } else if ($this->nextStepPlayer->getName() === $name) {
                $sumOfbetsOfNextStepingPlayer = $bet;
            }
        }

        if ($sumOfbetsOfLastRasingPlayer && $sumOfbetsOfNextStepingPlayer) {
            return $sumOfbetsOfLastRasingPlayer - $sumOfbetsOfNextStepingPlayer;
        }
        if ($sumOfbetsOfLastRasingPlayer) {
            return $sumOfbetsOfLastRasingPlayer;
        }
        
        return false;
    }

    private function takeDefaultBet()
    {
        //создаем массив вида игрок => дефолтная ставка
        foreach (Game::getAllPlayers() as $player) {
            $this->defaultBets[] = [
                "betMaker" => $player->getName(),
                "defaultBet" => $player->makeDefaultBet(self::DEFAULT_BET)
            ];
        }
        //добавляем дефолтные ставки к общей сумме кона
        foreach ($this->defaultBets as $item) {
            $this->sumOfDefaultBets += $item['defaultBet'];
        }
    }

    public function getSavingPlayers()
    {
        $arrplayersSaving = [];
        foreach ($this->playersSaving as $player) {
            $arrplayersSaving[] = $player->getName();
        }

        return $arrplayersSaving;
    }

    public function getRoundDefaultBets()
    {
        return $this->defaultBets;
    }

    public function getRoundBets()
    {
        return $this->bets;
    }

    public function getDistributor()
    {
        return $this->distributor;
    }

    public function setDistributor()
    {
        $players = Game::getAllPlayers();
        
        if ($this->id == 1) {
            $rand = rand(0, count($players) - 1);
            $this->distributor = $players[$rand];
            
            Game::setCurrentDistributor($this->distributor);
        } else {
            foreach ($players as $index => $player) {
                if (Game::getCurrentDistributor()->getId() == $player->getId()) {
                    if (array_key_exists($index + 1, $players)) {
                        $this->distributor = $players[$index + 1];
                        Game::setCurrentDistributor($this->distributor);
                        return;
                    } else {
                        $this->distributor = $players[0];
                        Game::setCurrentDistributor($this->distributor);
                        return;
                    }
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

    private function setNextStepPlayer()
    {
        //определяем индекс ходившего игрока
        foreach ($this->roundParameters as $index => $player) {
            if ($player['resourceId'] === $this->currentStepPlayer->getConnResourceId()) {
                $currentStepPlayerIndex = $index;
            }
        }

        if ($currentStepPlayerIndex === 0) {
            for ($i = 1; $i < count($this->roundParameters); $i ++) { 
                if ($this->roundParameters[$i]['state'] !== 'save') {
                    $nextStepPlayerResourceId = $this->roundParameters[$i]['resourceId'];
                    break;
                }
            }
        } else if ($currentStepPlayerIndex === count($this->roundParameters) - 1) {
            for ($i = 0; $i < count($this->roundParameters); $i ++) { 
                if ($this->roundParameters[$i]['state'] !== 'save') {
                    $nextStepPlayerResourceId = $this->roundParameters[$i]['resourceId'];
                    break;
                }
            }
        } else {
            for ($i = $currentStepPlayerIndex + 1; $i < count($this->roundParameters); $i ++) { 
                if ($this->roundParameters[$i]['state'] !== 'save') {
                    $nextStepPlayerResourceId = $this->roundParameters[$i]['resourceId'];
                    break;
                }
            }
            if (!isset($nextStepPlayerResourceId)) {
                for ($i = 0; $i < $currentStepPlayerIndex; $i ++) { 
                    if ($this->roundParameters[$i]['state'] !== 'save') {
                        $nextStepPlayerResourceId = $this->roundParameters[$i]['resourceId'];
                        break;
                    }
                }
            }
        }

        //находим в игроках данный resourceId
        foreach (Game::getAllPlayers() as $player) {
            if ($player->getConnResourceId() === $nextStepPlayerResourceId) {
                return $player;
            }
        }
    }

    public function takeBet(Player $playerSteping, int $bet)
    {
        
        if (isset($this->bets[$playerSteping->getName()])) {
            $this->bets[$playerSteping->getName()] += $bet;
            if ($bet) {
                $bet = $this->bets[$playerSteping->getName()];
            }
        } else {
            $this->bets[$playerSteping->getName()] = $bet;
        }
        
        //суммируем в общую кассу ставкиы
        $this->cashBox = \collect($this->bets)->sum();
        var_dump($this->cashBox);

        //игрок который ходит сейчас
        $players = Game::getAllPLayers();
        $this->currentStepPlayer = $playerSteping;

        foreach ($players as $index => $player) {
            if ($player == $this->currentStepPlayer) {
                $currentStepPlayerIndex = $index;
            }
        }

        //обновление параметров раунда и текущего состояния
        if ($bet > $this->lastBet) {
            $this->lastRasingPlayer = $playerSteping;

            foreach ($this->roundParameters as $index => $player) {
                if ($playerSteping->getConnResourceId() == $player['resourceId']) {
                    $this->roundParameters[$index]['state'] = 'raise';
                }
            }
        } else if ($bet === $this->lastBet && $bet > 0) {
            $this->lastCollatingPlayer = $playerSteping;

            foreach ($this->roundParameters as $index => $player) {
                if ($playerSteping->getConnResourceId() == $player['resourceId']) {
                    $this->roundParameters[$index]['state'] = 'collate';
                }
            }
        } else if ($bet === 0) {
            $this->lastSavingPlayer = $playerSteping;

            foreach ($this->roundParameters as $index => $player) {
                if ($playerSteping->getConnResourceId() == $player['resourceId']) {
                    $this->roundParameters[$index]['state'] = 'save';
                }
            }
        }

        if ($bet > 0) {
            $this->lastBet = $bet;
        } else if ($bet === 0) {
            $this->playersSaving[] = $playerSteping;
        }
        
        //игрок который будет делать следующий ход
        $this->nextStepPlayer = $this->setNextStepPlayer();

        $playerOpenCardAbility = $this->checkRoundParameters($playerSteping);

        if ($playerOpenCardAbility) {
            foreach (Game::getAllPlayers() as $key => $player) {
                if ($player->getConnResourceId() == $playerOpenCardAbility) {
                    $this->playerOpenCardAbility = $player;
                }
            }
        }
    }

    private function checkRoundParameters(Player $playerSteping)
    {
        //проверяем, все ли сходили
        foreach ($this->roundParameters as $index => $player) {
            if (!$player['state']) {
                return null;
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

        //если два и более raise и все сходили, тогда удаляем все до последнего первого raise
        if ($countOfRaises >= 2) {
            $this->isFirstRoundOfSales = false;

            if ($playerSteping->getConnResourceId() !== $this->lastRasingPlayer->getConnResourceId()) {
                foreach ($this->roundParameters as $index => $player) {
                    if ($player['resourceId'] === $playerSteping->getConnResourceId()) {
                        $indexOfCurrentStepPlayer = $index;
                    } else if ($this->lastRasingPlayer->getConnResourceId() === $player['resourceId']) {
                        $indexOfLastRasingPlayer = $index;
                    }
                }
            } else {
                foreach ($this->roundParameters as $index => $player) {
                    if ($player['resourceId'] === $playerSteping->getConnResourceId()) {
                        $indexOfCurrentStepPlayer = $index;
                        $indexOfLastRasingPlayer = $index;
                    }
                }
            }
            

            if ($indexOfCurrentStepPlayer > $indexOfLastRasingPlayer) {
                for ($i = $indexOfCurrentStepPlayer + 1; $i < count($this->roundParameters); $i++) { 
                    if ($this->roundParameters[$i]['state'] === 'collate' || $this->roundParameters[$i]['state'] === 'raise') {
                        $this->roundParameters[$i]['state'] = null;
                    }
                }
                for ($i = 0; $i < $indexOfLastRasingPlayer; $i++) { 
                    if ($this->roundParameters[$i]['state'] === 'collate' || $this->roundParameters[$i]['state'] === 'raise') {
                        $this->roundParameters[$i]['state'] = null;
                    }
                }
            } else if ($indexOfCurrentStepPlayer < $indexOfLastRasingPlayer) {
                for ($i = $indexOfCurrentStepPlayer + 1; $i < $indexOfLastRasingPlayer; $i++) { 
                    if ($this->roundParameters[$i]['state'] === 'collate' || $this->roundParameters[$i]['state'] === 'raise') {
                        $this->roundParameters[$i]['state'] = null;
                    }
                }
            } else {
                if ($indexOfCurrentStepPlayer === count($this->roundParameters) - 1) {
                    for ($i = 0; $i < $indexOfLastRasingPlayer; $i++) { 
                        if ($this->roundParameters[$i]['state'] === 'collate' || $this->roundParameters[$i]['state'] === 'raise') {
                            $this->roundParameters[$i]['state'] = null;
                        }
                    }
                } else if ($indexOfCurrentStepPlayer === 0) {
                    for ($i = $indexOfCurrentStepPlayer + 1; $i < count($this->roundParameters); $i++) { 
                        if ($this->roundParameters[$i]['state'] === 'collate' || $this->roundParameters[$i]['state'] === 'raise') {
                            $this->roundParameters[$i]['state'] = null;
                        }
                    }
                } else {
                    for ($i = $indexOfCurrentStepPlayer + 1; $i < count($this->roundParameters); $i++) { 
                        if ($this->roundParameters[$i]['state'] === 'collate' || $this->roundParameters[$i]['state'] === 'raise') {
                            $this->roundParameters[$i]['state'] = null;
                        }
                    }
                    for ($i = 0; $i < $indexOfCurrentStepPlayer; $i++) { 
                        if ($this->roundParameters[$i]['state'] === 'collate' || $this->roundParameters[$i]['state'] === 'raise') {
                            $this->roundParameters[$i]['state'] = null;
                        }
                    }
                }
                var_dump($this->roundParameters);
            }
            
            return $playerOpenCardAbility;
        }
        
        
        if ($countOfRaises < 2 && $countOfCollates > 0) {
            if ($this->isFirstRoundOfSales) {
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
            } else {
                if (isset($this->lastSavingPlayer) && $playerSteping->getId() === $this->lastSavingPlayer->getId()) {
                    $playerOpenCardAbility = $this->lastRasingPlayer->getConnResourceId();
                } else {
                    $playerOpenCardAbility = $this->lastCollatingPlayer->getConnResourceId();
                }
            }
        } else if ($countOfRaises < 2 && $countOfCollates === 0) {
            if ($this->isFirstRoundOfSales) {
                foreach ($this->roundParameters as $index => $player) {
                    if ($player['state'] === 'raise') {
                        $this->playerTakingConWithoutShowingUp = $player['resourceId'];
                    }
                }
            } else {
                foreach ($this->roundParameters as $index => $player) {
                    if ($player['state'] === 'raise') {
                        $playerOpenCardAbility = $player['resourceId'];
                    }
                }
            }
            
        } else if ($countOfRaises > 1) {
            foreach ($this->roundParameters as $index => $player) {
                if ($player['state'] === 'raise') {
                    $this->playersRaising[] = $player['resourceId'];
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

    public function getWinner()
    {
        return $this->winner;
    }

    public function getWinnerAfterOpeningCards()
    {
        if (count($this->winnerAfterOpeningCards) === 1) {
            return [$this->winnerAfterOpeningCards[0]->getName()];
        } else {
            foreach ($this->winnerAfterOpeningCards as $player) {
                $playersWinners[] = $player->getName();
            }

            return $playersWinners;
        }
        
    }

    public function setWinnerAfterOpeningCards()
    {
        $players = Game::getAllPlayers();
        
        $maxValue = \collect(Game::getPlayersPointsAfterOpeningCards())->max('points');

        foreach ($players as $player) {
            if ($player->getCardsValueAfterOpening() === $maxValue) {
                $playersWithMaxPoints[] = $player;
            }
        }

        $this->winnerAfterOpeningCards = $playersWithMaxPoints;
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

    public function checkUserCardsValue()
    {
        Diller::checkUserCardsValue();
    }

    public function endRoundWithoutShowingUp()
    {
        $players = Game::getAllPlayers();
        foreach ($players as $player) {
            if ($player->getConnResourceId() == $this->playerTakingConWithoutShowingUp) {
                $this->winner = $player;
            }
        }
        
        foreach ($players as $player) {
            if ($player->getId() == $this->winner->getId()) {
                $userFromDb = User::where('id', $this->winner->getId())->first();
                $userFromDb->balance = $this->winner->getBalance() + $this->cashBox;
                $userFromDb->save();
                $player->setBalance();
            } else {
                $userFromDb = User::where('id', $player->getId())->first();
                $userFromDb->balance = $player->getBalance();
                $userFromDb->save();
                $player->setBalance();
            }
        }

        $this->endRoundWithoutShowingUp = true;

        Game::endCurrentRound();
    }

    public function endRoundAfterOpeningCards()
    {
        $players = Game::getAllPlayers();

        if (count($this->winnerAfterOpeningCards) === 1) {
            foreach ($players as $player) {
                if ($player->getConnResourceId() == $this->winnerAfterOpeningCards[0]->getConnResourceId()) {
                    $this->winner = $player;
                }
            }
            var_dump($this->cashBox);
            var_dump($this->winner->getBalance());
            foreach ($players as $player) {
                if ($player->getId() == $this->winner->getId()) {
                    $userFromDb = User::where('id', $this->winner->getId())->first();
                    $userFromDb->balance = 
                        $this->winner->getBalance() + $this->cashBox + $this->sumOfDefaultBets;
                    $userFromDb->save();
                    $player->setBalance();
                } else {
                    $userFromDb = User::where('id', $player->getId())->first();
                    $userFromDb->balance = $player->getBalance();
                    $userFromDb->save();
                    $player->setBalance();
                }
            }
    
            
        } else {
            $arrOfIdsOfWInners = [];

            foreach ($this->winnerAfterOpeningCards as $player) {
                $arrOfIdsOfWInners[] = $player->getId();
            }

            foreach ($players as $player) {
                if (in_array($player->getId(), $arrOfIdsOfWInners)) {
                    $userFromDb = User::where('id', $player->getId())->first();
                    $userFromDb->balance = $player->getBalance()+ round(($this->cashBox + $this->sumOfDefaultBets)/ count($arrOfIdsOfWInners));
                    $userFromDb->save();
                    $player->setBalance();
                } else {
                    $userFromDb = User::where('id', $player->getId())->first();
                    $userFromDb->balance = $player->getBalance();
                    $userFromDb->save();
                    $player->setBalance();
                }
            }
        }
        
        $this->endRoundAfterOpeningCarts = true;

        Game::endCurrentRound();
    }

    public function isRoundEndWithoutShowingUp()
    {
        return  $this->endRoundWithoutShowingUp;
    }
}