<?php

namespace App\GameRequisits;

use App\GameRequisits\Cards\Diller;
use App\GameRequisits\Game;
use App\GameRequisits\Users\Player;
use App\User;
use App\Tax;
use DB;

class Round
{
    private $id;
    private const DEFAULT_BET = 50;
    private $defaultBets = [];
    private $sumOfDefaultBets = 0;
    private $bets = [];
    private $cashBox;
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
    private $taxSum = [];
    private $status;

    public function __construct(int $currentRound)
    {
        $this->id = $currentRound;
    }

    public function getId()
    {
        return $this->id;
    }

    public function start()
    {
        Diller::distribute();
        $this->takeDefaultBet();
        $this->setDistributor();
        $this->setFirstWordPlayer();
        $this->setInitRoundParameters();
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function setCashBox(int $cashBox)
    {
        $this->cashBox = $cashBox;
    }

    public function getStatus()
    {
        return $this->status;
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

    public function getTax()
    {
        return $this->taxSum;
    }

    public function getTaxSum()
    {
        return array_sum($this->taxSum);
    }

    private function substractTax()
    {
        $taxSumFromCashBox = $this->calculateTax($this->cashBox, Game::getTax());
        $taxSumFromDefautBets = $this->calculateTax($this->sumOfDefaultBets, Game::getTax());
        $this->cashBox -= $taxSumFromCashBox;
        $this->sumOfDefaultBets -= $taxSumFromDefautBets;

        $roundId = $this->id;
        $game = DB::table('game')->orderBy('id', 'desc')->first();
        $tax = new Tax();
        $tax->game_number = $game->id;
        $tax->round_number = $roundId;
        $tax->sum = $taxSumFromCashBox + $taxSumFromDefautBets;

        $tax->save();
    }

    private function calculateTax(int $from, int $tax)
    {
        $percent = $from / 100 * $tax;
        $this->taxSum[] = $percent;
        return $percent;
    }

    private function takeDefaultBet()
    {
        if (Game::isCooking()) {
            //создаем массив вида игрок => дефолтная ставка
            foreach (Game::getAllPlayers() as $player) {
                $this->defaultBets[] = [
                    "betMaker" => $player->getName(),
                    "defaultBet" => 
                        $player->makeDefaultBet(self::DEFAULT_BET)
                ];
            }

            //добавляем дефолтные ставки к общей сумме кона
            foreach ($this->defaultBets as $item) {
                $this->sumOfDefaultBets += $item['defaultBet'];
            }
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
            if ($player['id'] === $this->currentStepPlayer->getId()) {
                $currentStepPlayerIndex = $index;
            }
        }

        if ($currentStepPlayerIndex === 0) {
            for ($i = 1; $i < count($this->roundParameters); $i ++) { 
                if ($this->roundParameters[$i]['state'] !== 'save') {
                    $nextStepPlayerId = $this->roundParameters[$i]['id'];
                    break;
                }
            }
        } else if ($currentStepPlayerIndex === count($this->roundParameters) - 1) {
            for ($i = 0; $i < count($this->roundParameters); $i ++) { 
                if ($this->roundParameters[$i]['state'] !== 'save') {
                    $nextStepPlayerId = $this->roundParameters[$i]['id'];
                    break;
                }
            }
        } else {
            for ($i = $currentStepPlayerIndex + 1; $i < count($this->roundParameters); $i ++) { 
                if ($this->roundParameters[$i]['state'] !== 'save') {
                    $nextStepPlayerId = $this->roundParameters[$i]['id'];
                    break;
                }
            }
            if (!isset($nextStepPlayerId)) {
                for ($i = 0; $i < $currentStepPlayerIndex; $i ++) { 
                    if ($this->roundParameters[$i]['state'] !== 'save') {
                        $nextStepPlayerId = $this->roundParameters[$i]['id'];
                        break;
                    }
                }
            }
        }

        //находим в игроках данный resourceId
        foreach (Game::getAllPlayers() as $player) {
            if ($player->getId() === $nextStepPlayerId) {
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

        //игрок который ходит сейчас
        $players = Game::getAllPLayers();
        $this->currentStepPlayer = $playerSteping;

        foreach ($players as $index => $player) {
            if ($player->getId() == $this->currentStepPlayer->getId()) {
                $currentStepPlayerIndex = $index;
            }
        }

        //обновление параметров раунда и текущего состояния
        if ($bet > $this->lastBet) {
            $this->lastRasingPlayer = $playerSteping;

            foreach ($this->roundParameters as $index => $player) {
                if ($playerSteping->getId() == $player['id']) {
                    $this->roundParameters[$index]['state'] = 'raise';
                }
            }
        } else if ($bet === $this->lastBet && $bet > 0) {
            $this->lastCollatingPlayer = $playerSteping;

            foreach ($this->roundParameters as $index => $player) {
                if ($playerSteping->getId() == $player['id']) {
                    $this->roundParameters[$index]['state'] = 'collate';
                }
            }
        } else if ($bet === 0) {
            $this->lastSavingPlayer = $playerSteping;

            foreach ($this->roundParameters as $index => $player) {
                if ($playerSteping->getId() == $player['id']) {
                    $this->roundParameters[$index]['state'] = 'save';
                }
            }
        }

        if ($bet > 0) {
            $this->lastBet = $bet;
        } else if ($bet === 0) {
            $this->playersSaving[] = $playerSteping;
        }
        
        $playerOpenCardAbility = $this->checkRoundParameters($playerSteping);
        //игрок который будет делать следующий ход
        $this->nextStepPlayer = $this->setNextStepPlayer();

        if ($playerOpenCardAbility) {
            Game::setLastRoundCashBox($this->cashBox + $this->sumOfDefaultBets);
            foreach (Game::getAllPlayers() as $key => $player) {
                if ($player->getId() == $playerOpenCardAbility) {
                    $this->playerOpenCardAbility = $player;
                }
            }
        }
    }

    private function checkRoundParameters(Player $playerSteping)
    {
        $countOfSaves = 0;
        $playerOpenCardAbility = null;
        //если все пасанули кроме того, за кем последний ход, то он забирает не вскрываясь
        foreach ($this->roundParameters as $index => $player) {
            if ($player['state'] === 'save') {
                $countOfSaves += 1;
            }
        }
        if ($countOfSaves === count($this->roundParameters) - 1) {
            foreach ($this->roundParameters as $index => $player) {
                if ($player['state'] !== 'save') {
                    $this->playerTakingConWithoutShowingUp = $player['id'];
                }
            }

            return $playerOpenCardAbility;
        }
        
        //проверяем, все ли сходили
        foreach ($this->roundParameters as $index => $player) {
            if (!$player['state']) {
                return null;
            }
        }
        
        //если да, то проверяем пора ли завершать раунд
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

            if ($playerSteping->getId() !== $this->lastRasingPlayer->getId()) {
                foreach ($this->roundParameters as $index => $player) {
                    if ($player['id'] === $playerSteping->getId()) {
                        $indexOfCurrentStepPlayer = $index;
                    } else if ($this->lastRasingPlayer->getId() === $player['id']) {
                        $indexOfLastRasingPlayer = $index;
                    }
                }
            } else {
                foreach ($this->roundParameters as $index => $player) {
                    if ($player['id'] === $playerSteping->getId()) {
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
            }
            
            return $playerOpenCardAbility;
        }
        
        
        if ($countOfRaises < 2 && $countOfCollates > 0) {
            if ($this->isFirstRoundOfSales) {
                foreach ($this->roundParameters as $index => $player) {
                    if ($player['state'] === 'raise') {
                        if (isset($this->roundParameters[$index - 1])) {
                            if ($this->roundParameters[$index - 1]['state'] === 'save') {
                                $playerOpenCardAbility = $player['id'];
                            } else if ($this->roundParameters[$index - 1]['state'] === 'collate') {
                                $playerOpenCardAbility = $this->roundParameters[$index - 1]['id'];
                            }
                        } else {
                            if ($this->roundParameters[count($this->roundParameters) - 1]['state'] === 'save') {
                                $playerOpenCardAbility = $player['id'];
                            } else if ($this->roundParameters[count($this->roundParameters) - 1]['state'] === 'collate') {
                                $playerOpenCardAbility = $this->roundParameters[count($this->roundParameters) - 1]['id'];
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
                foreach ($this->roundParameters as $index => $player) {
                    if ($player['state'] === 'raise') {
                        $this->playerTakingConWithoutShowingUp = $player['id'];
                    }
                }
            } else {
                foreach ($this->roundParameters as $index => $player) {
                    if ($player['state'] === 'raise') {
                        $playerOpenCardAbility = $player['id'];
                    }
                }
            }
            
        } else if ($countOfRaises > 1) {
            foreach ($this->roundParameters as $index => $player) {
                if ($player['state'] === 'raise') {
                    $this->playersRaising[] = $player['id'];
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
                'id' => $player->getId(),
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

    public function getPlayerTakingConWithoutShowingUp()
    {
        if ($this->playerTakingConWithoutShowingUp) {
            foreach (Game::getAllPlayers() as $player) {
                if ($player->getId() === $this->playerTakingConWithoutShowingUp) {
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
            if ($player->getId() == $this->playerTakingConWithoutShowingUp) {
                $this->winner = $player;
            }
        }
        //снимаем налог
        $this->substractTax();

        foreach ($players as $player) {
            if ($player->getId() == $this->winner->getId()) {
                $userFromDb = User::where('id', $this->winner->getId())->first();
                $userFromDb->balance = $this->winner->getBalance() + $this->cashBox +$this->sumOfDefaultBets;
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
        foreach (Game::getAllPlayers() as $player) {
            if ($player->getId() === $this->playerTakingConWithoutShowingUp) {
                Game::setLastRoundWinner([$player]);
            }
        }
        Game::endCurrentRound();
    }

    public function shareCashBoxAfterOpening()
    {
       
        $players = Game::getAllPLayers(); 

        $arrOfIdsOfWInners = [];

        foreach ($this->winnerAfterOpeningCards as $player) {
            $arrOfIdsOfWInners[] = $player->getId();
        }

        //снимаем налог
        $this->substractTax();
        
        foreach ($players as $player) {
            if (in_array($player->getId(), $arrOfIdsOfWInners)) {
                $userFromDb = User::where('id', $player->getId())->first();
                $userFromDb->balance =
                    $player->getBalance() + round(($this->cashBox + $this->sumOfDefaultBets)/ count($arrOfIdsOfWInners));
                $userFromDb->save();
                $player->setBalance();
            } else {
                $userFromDb = User::where('id', $player->getId())->first();
                $userFromDb->balance = $player->getBalance();
                $userFromDb->save();
                $player->setBalance();
            }
        }
        $winners = [];

        foreach (Game::getAllPlayers() as $player) {
            if (in_array($player->getId(), $arrOfIdsOfWInners)) {
                $winners[] = $player;
            }
        }
        Game::setLastRoundWinner($winners);
        Game::endCurrentRound();
    }

    public function endRoundAfterOpeningCards()
    {
        $players = Game::getAllPlayers();

        foreach ($players as $player) {
            if ($player->getConnResourceId() == $this->winnerAfterOpeningCards[0]->getConnResourceId()) {
                $this->winner = $player;
            }
        }
            
        //снимаем налог
        $this->substractTax();

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

        Game::setLastRoundWinner([$this->winner]);
        Game::endCurrentRound();
    }

    public function isRoundEndWithoutShowingUp()
    {
        return  $this->endRoundWithoutShowingUp;
    }
}