<?php

namespace App\GameRequisits;

use App\GameRequisits\Cards\Diller;
use App\GameRequisits\Users\Player;
use App\User;

class Cooking
{
    private $id;
    private $players;
    private $cashBox;
    private $bets = [];
    private $savingPlayers = [];
    private $lastRasingPlayer;
    private $lastSavingPlayer;
    private $lastCollatingPlayer;
    private $distributor;
    private $nextStepPlayer;
    private $currentStepPlayer;
    private $cookingParameters = [];
    private $firstWordPlayer;
    private $playersSaving = [];
    private $playerOpenCardAbility;
    private $playerTakingConWithoutShowingUp;
    private $lastBet = 0;
    private $winner;
    private $isFirstRoundOfSales = true;
    public $winnerAfterOpeningCards;
    private $lastRoundCashBox;

    public function __construct(int $id, array $players, int $sum)
    {
        $this->id = $id;
        $this->players = $players;
        $this->lastRoundCashBox += $sum;
    }

    public function start()
    {
        Diller::distribute($this->players);
        $this->setDistributor();
        $this->setFirstWordPlayer();
        $this->setInitRoundParameters();
    }

    public function getId()
    {
        return $this->id;
    }

    private function setInitRoundParameters()
    {
        foreach ($this->players as $index => $player) {
            $this->cookingParameters[] = [
                'id' => $player->getId(),
                'state' => null,
                'name' => $player->getName(),
                'player' => $player
            ];
        }
    }

    public function setDistributor()
    {
        if ($this->id == 1) {
            $rand = rand(0, count($this->players) - 1);
            $this->distributor = $this->players[$rand];
            
            Game::setCurrentDistributor($this->distributor);
        } else {
            $winners = Game::getLastRoundWinner();

            if (count($winners) === 1) {
                $this->distributor = $winners[0];
                Game::setCurrentDistributor($this->distributor);
            } else {
                $rand = rand(0, count($winners) - 1);
                $this->distributor = $winners[$rand];
                Game::setCurrentDistributor($this->distributor);
            }
        }
    }

    public function setFirstWordPlayer()
    {
        foreach ($this->players as $index => $player) {
            if ($player->getId() === $this->distributor->getId()) {
                $currentDistributorIndex = $index;
            }
        }
        
        if (isset($this->players[$currentDistributorIndex + 1])) {
            $this->firstWordPlayer = $this->players[$currentDistributorIndex + 1];
        } else {
            $this->firstWordPlayer = $this->players[0];
        }

        Game::setCurrentFirstWordPlayer($this->firstWordPlayer);
    }

    private function setNextStepPlayer()
    {
        //определяем индекс ходившего игрока
        foreach ($this->cookingParameters as $index => $player) {
            if ($player['id'] === $this->currentStepPlayer->getId()) {
                $currentStepPlayerIndex = $index;
            }
        }

        if ($currentStepPlayerIndex === 0) {
            for ($i = 1; $i < count($this->cookingParameters); $i ++) { 
                if ($this->cookingParameters[$i]['state'] !== 'save') {
                    $nextStepPlayerId = $this->cookingParameters[$i]['id'];
                    break;
                }
            }
        } else if ($currentStepPlayerIndex === count($this->cookingParameters) - 1) {
            for ($i = 0; $i < count($this->cookingParameters); $i ++) { 
                if ($this->cookingParameters[$i]['state'] !== 'save') {
                    $nextStepPlayerId = $this->cookingParameters[$i]['id'];
                    break;
                }
            }
        } else {
            for ($i = $currentStepPlayerIndex + 1; $i < count($this->cookingParameters); $i ++) { 
                if ($this->cookingParameters[$i]['state'] !== 'save') {
                    $nextStepPlayerId = $this->cookingParameters[$i]['id'];
                    break;
                }
            }
            if (!isset($nextStepPlayerId)) {
                for ($i = 0; $i < $currentStepPlayerIndex; $i ++) { 
                    if ($this->cookingParameters[$i]['state'] !== 'save') {
                        $nextStepPlayerId = $this->cookingParameters[$i]['id'];
                        break;
                    }
                }
            }
        }

        //находим в игроках данный Id
        foreach ($this->players as $player) {
            if ($player->getId() === $nextStepPlayerId) {
                return $player;
            }
        }
    }

    public function getNextStepPlayerToCollate()
    {
        $sumOfbetsOfLastRasingPlayer = 0;
        $sumOfbetsOfNextStepingPlayer = 0;

        foreach ($this->bets as $name => $bet) {
            if (isset($this->lastRasingPlayer) && $this->lastRasingPlayer->getName() === $name) {
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

    public function getRoundCashBox()
    {
        return $this->cashBox;
    }
    
    public function getSavingPlayers()
    {
        $arrplayersSaving = [];
        foreach ($this->playersSaving as $player) {
            $arrplayersSaving[] = $player->getName();
        }

        return $arrplayersSaving;
    }

    public function getRoundBets()
    {
        return $this->bets;
    }

    public function getWinner()
    {
        return $this->winner;
    }

    public function getBalanceOfAllPlayers()
    {
        foreach ($this->players as $player) {
            $balanceOfAllPlayers[$player->getName()] = $player->getBalance();
        }

        return $balanceOfAllPlayers;
    }

    public function getCurrentStepPlayer()
    {
        return $this->currentStepPlayer;
    }

    public function getNextStepPlayer()
    {
        return $this->nextStepPlayer;
    }

    public function getPlayersNormalized(): array
    {
        $players = [];
        foreach ($this->players as $player) {
            $players[] = $player->getName();
        }
        return $players;
    }

    public function getCashBox()
    {
        return $this->cashBox;
    }

    public function getPlayerTakingConWithoutShowingUp()
    {
        if ($this->playerTakingConWithoutShowingUp) {
            foreach ($this->players as $player) {
                if ($player->getId() === $this->playerTakingConWithoutShowingUp) {
                    return $player->getName();
                }
            }
        }
        return false;
    }

    public function getPlayerOpenCardAbility()
    {
        if ($this->playerOpenCardAbility) {
            return $this->playerOpenCardAbility->getName();
        }
        return false;
    }

    public function getLastBet()
    {
        return $this->lastBet;
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
        $this->cashBox += $this->lastRoundCashBox;
        //игрок который ходит сейчас
        $players = $this->players;
        $this->currentStepPlayer = $playerSteping;

        foreach ($players as $index => $player) {
            if ($player->getId() == $this->currentStepPlayer->getId()) {
                $currentStepPlayerIndex = $index;
            }
        }

        //обновление параметров раунда и текущего состояния
        if ($bet > $this->lastBet) {
            $this->lastRasingPlayer = $playerSteping;

            foreach ($this->cookingParameters as $index => $player) {
                if ($playerSteping->getId() == $player['id']) {
                    $this->cookingParameters[$index]['state'] = 'raise';
                }
            }
        } else if ($bet === $this->lastBet && $bet > 0) {
            $this->lastCollatingPlayer = $playerSteping;

            foreach ($this->cookingParameters as $index => $player) {
                if ($playerSteping->getId() == $player['id']) {
                    $this->cookingParameters[$index]['state'] = 'collate';
                }
            }
        } else if ($bet === 0) {
            $this->lastSavingPlayer = $playerSteping;

            foreach ($this->cookingParameters as $index => $player) {
                if ($playerSteping->getId() == $player['id']) {
                    $this->cookingParameters[$index]['state'] = 'save';
                }
            }
        }
        
        if ($bet > 0) {
            $this->lastBet = $bet;
        } else if ($bet === 0) {
            $this->playersSaving[] = $playerSteping;
        }
        
        $playerOpenCardAbility = $this->checkCookingParameters($playerSteping);
        //игрок который будет делать следующий ход
        $this->nextStepPlayer = $this->setNextStepPlayer();

        if ($playerOpenCardAbility) {
            Game::setLastRoundCashBox($this->cashBox);
            foreach ($this->players as $key => $player) {
                if ($player->getId() == $playerOpenCardAbility) {
                    $this->playerOpenCardAbility = $player;
                }
            }
        }
    }

    private function checkCookingParameters(Player $playerSteping)
    {
        $countOfSaves = 0;
        $playerOpenCardAbility = null;
        //если все пасанули кроме того, за кем последний ход, то он забирает не вскрываясь
        foreach ($this->cookingParameters as $index => $player) {
            if ($player['state'] === 'save') {
                $countOfSaves += 1;
            }
        }
        if ($countOfSaves === count($this->cookingParameters) - 1) {
            foreach ($this->cookingParameters as $index => $player) {
                if ($player['state'] !== 'save') {
                    $this->playerTakingConWithoutShowingUp = $player['id'];
                }
            }

            return $playerOpenCardAbility;
        }
        
        //проверяем, все ли сходили
        foreach ($this->cookingParameters as $index => $player) {
            if (!$player['state']) {
                return null;
            }
        }
        
        //если да, то проверяем пора ли завершать раунд
        $countOfRaises = 0;
        $countOfCollates = 0;

        foreach ($this->cookingParameters as $index => $player) {
            if ($player['state'] === 'raise') {
                $countOfRaises += 1;
            }
        }

        foreach ($this->cookingParameters as $index => $player) {
            if ($player['state'] === 'collate') {
                $countOfCollates += 1;
            }
        }

        //если два и более raise и все сходили, тогда удаляем все до последнего первого raise
        if ($countOfRaises >= 2) {
            $this->isFirstRoundOfSales = false;

            if ($playerSteping->getId() !== $this->lastRasingPlayer->getId()) {
                foreach ($this->cookingParameters as $index => $player) {
                    if ($player['id'] === $playerSteping->getId()) {
                        $indexOfCurrentStepPlayer = $index;
                    } else if ($this->lastRasingPlayer->getId() === $player['id']) {
                        $indexOfLastRasingPlayer = $index;
                    }
                }
            } else {
                foreach ($this->cookingParameters as $index => $player) {
                    if ($player['id'] === $playerSteping->getId()) {
                        $indexOfCurrentStepPlayer = $index;
                        $indexOfLastRasingPlayer = $index;
                    }
                }
            }
            

            if ($indexOfCurrentStepPlayer > $indexOfLastRasingPlayer) {
                for ($i = $indexOfCurrentStepPlayer + 1; $i < count($this->cookingParameters); $i++) { 
                    if ($this->cookingParameters[$i]['state'] === 'collate' || $this->cookingParameters[$i]['state'] === 'raise') {
                        $this->cookingParameters[$i]['state'] = null;
                    }
                }
                for ($i = 0; $i < $indexOfLastRasingPlayer; $i++) { 
                    if ($this->cookingParameters[$i]['state'] === 'collate' || $this->cookingParameters[$i]['state'] === 'raise') {
                        $this->cookingParameters[$i]['state'] = null;
                    }
                }
            } else if ($indexOfCurrentStepPlayer < $indexOfLastRasingPlayer) {
                for ($i = $indexOfCurrentStepPlayer + 1; $i < $indexOfLastRasingPlayer; $i++) { 
                    if ($this->cookingParameters[$i]['state'] === 'collate' || $this->cookingParameters[$i]['state'] === 'raise') {
                        $this->cookingParameters[$i]['state'] = null;
                    }
                }
            } else {
                if ($indexOfCurrentStepPlayer === count($this->cookingParameters) - 1) {
                    for ($i = 0; $i < $indexOfLastRasingPlayer; $i++) { 
                        if ($this->cookingParameters[$i]['state'] === 'collate' || $this->cookingParameters[$i]['state'] === 'raise') {
                            $this->cookingParameters[$i]['state'] = null;
                        }
                    }
                } else if ($indexOfCurrentStepPlayer === 0) {
                    for ($i = $indexOfCurrentStepPlayer + 1; $i < count($this->cookingParameters); $i++) { 
                        if ($this->cookingParameters[$i]['state'] === 'collate' || $this->cookingParameters[$i]['state'] === 'raise') {
                            $this->cookingParameters[$i]['state'] = null;
                        }
                    }
                } else {
                    for ($i = $indexOfCurrentStepPlayer + 1; $i < count($this->cookingParameters); $i++) { 
                        if ($this->cookingParameters[$i]['state'] === 'collate' || $this->cookingParameters[$i]['state'] === 'raise') {
                            $this->cookingParameters[$i]['state'] = null;
                        }
                    }
                    for ($i = 0; $i < $indexOfCurrentStepPlayer; $i++) { 
                        if ($this->cookingParameters[$i]['state'] === 'collate' || $this->cookingParameters[$i]['state'] === 'raise') {
                            $this->cookingParameters[$i]['state'] = null;
                        }
                    }
                }
            }
            
            return $playerOpenCardAbility;
        }
        
        
        if ($countOfRaises < 2 && $countOfCollates > 0) {
            if ($this->isFirstRoundOfSales) {
                foreach ($this->cookingParameters as $index => $player) {
                    if ($player['state'] === 'raise') {
                        if (isset($this->cookingParameters[$index - 1])) {
                            if ($this->cookingParameters[$index - 1]['state'] === 'save') {
                                $playerOpenCardAbility = $player['id'];
                            } else if ($this->cookingParameters[$index - 1]['state'] === 'collate') {
                                $playerOpenCardAbility = $this->cookingParameters[$index - 1]['id'];
                            }
                        } else {
                            if ($this->cookingParameters[count($this->cookingParameters) - 1]['state'] === 'save') {
                                $playerOpenCardAbility = $player['id'];
                            } else if ($this->cookingParameters[count($this->cookingParameters) - 1]['state'] === 'collate') {
                                $playerOpenCardAbility = $this->cookingParameters[count($this->cookingParameters) - 1]['id'];
                            }
                        }
                    }
                }
            } else {
                if (isset($this->lastSavingPlayer) && $playerSteping->getId() === $this->lastSavingPlayer->getId()) {
                    $playerOpenCardAbility = $this->lastRasingPlayer->getId();
                } else {
                    $playerOpenCardAbility = $this->lastCollatingPlayer->getId();
                }
            }
        } else if ($countOfRaises < 2 && $countOfCollates === 0) {
            if ($this->isFirstRoundOfSales) {
                foreach ($this->cookingParameters as $index => $player) {
                    if ($player['state'] === 'raise') {
                        $this->playerTakingConWithoutShowingUp = $player['id'];
                    }
                }
            } else {
                foreach ($this->cookingParameters as $index => $player) {
                    if ($player['state'] === 'raise') {
                        $playerOpenCardAbility = $player['id'];
                    }
                }
            }
            
        } else if ($countOfRaises > 1) {
            foreach ($this->cookingParameters as $index => $player) {
                if ($player['state'] === 'raise') {
                    $this->playersRaising[] = $player['id'];
                }
            }
        }
        return $playerOpenCardAbility;
    }

    public function checkUserCardsValue()
    {
        Diller::checkUserCardsValue($this->players);
    }
    
    public function setWinnerAfterOpeningCards()
    {
        $players = $this->players;
        
        $maxValue = \collect(Game::getPlayersPointsAfterOpeningCards())->max('points');
        $playersWithMaxPoints = [];
        foreach ($players as $player) {
            if (
                $player->getCardsValueAfterOpening() == $maxValue 
                && !in_array($player->getName(), $this->getSavingPlayers())
                ) {
                $playersWithMaxPoints[] = $player;
            }
        }
        Game::setLastRoundWinner($playersWithMaxPoints);
        $this->winnerAfterOpeningCards = $playersWithMaxPoints;
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

    public function endRoundAfterOpeningCards()
    {
        $players = $this->players;

        $this->winner = Game::getLastRoundWinner()[0];

        foreach ($players as $player) {
            if ($player->getId() == $this->winner->getId()) {
                $userFromDb = User::find($this->winner->getId());
                $userFromDb->balance = 
                    $this->winner->getBalance() + Game::getLastRoundCashBox();
                $userFromDb->save();
            } else {
                $userFromDb = User::find($player->getId());
                $userFromDb->balance = $player->getBalance();
                $userFromDb->save();
            }
        }
        //снимаем налог
        Game::chargeTax();
        
        Game::setLastRoundWinner([$this->winner]);
        Game::endCurrentRound();
    }

    public function shareCashBoxAfterOpening()
    {
        $players = $this->players; 
        $arrOfIdsOfWInners = [];

        foreach ($this->winnerAfterOpeningCards as $player) {
            $arrOfIdsOfWInners[] = $player->getId();
        }

        //снимаем налог
        //$this->substractTax();
        
        foreach ($players as $player) {
            if (in_array($player->getId(), $arrOfIdsOfWInners)) {
                $userFromDb = User::find($player->getId());
                $userFromDb->balance =
                    $player->getBalance() + round(Game::getLastRoundCashBox() / count($arrOfIdsOfWInners));
                $userFromDb->save();
            } else {
                $userFromDb = User::find($player->getId());
                $userFromDb->balance = $player->getBalance();
                $userFromDb->save();
            }
        }
        $winners = [];

        foreach (Game::getAllPlayers() as $player) {
            if (in_array($player->getId(), $arrOfIdsOfWInners)) {
                $winners[] = $player;
            }
        }
        //снимаем налог
        Game::chargeTax();
        
        Game::setLastRoundWinner($winners);
        Game::endCurrentRound();
    }

    public function endRoundWithoutShowingUp()
    {
        $players = $this->players;

        foreach ($players as $player) {
            if ($player->getId() == $this->playerTakingConWithoutShowingUp) {
                $this->winner = $player;
            }
        }

        foreach ($players as $player) {
            if ($player->getId() == $this->winner->getId()) {
                $userFromDb = User::find($this->winner->getId());
                $userFromDb->balance = $this->winner->getBalance() + Game::getLastRoundCashBox();
                $userFromDb->save();
            } else {
                $userFromDb = User::find($player->getId());
                $userFromDb->balance = $player->getBalance();
                $userFromDb->save();
            }
        }

        $this->endRoundWithoutShowingUp = true;
        foreach (Game::getAllPlayers() as $player) {
            if ($player->getId() === $this->playerTakingConWithoutShowingUp) {
                Game::setLastRoundWinner([$player]);
            }
        }
        //снимаем налог
        Game::chargeTax();

        Game::endCurrentRound();
    }

    public function isRoundEndWithoutShowingUp()
    {
        return  $this->endRoundWithoutShowingUp;
    }
}