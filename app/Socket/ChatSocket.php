<?php

namespace App\Socket;

use App\Socket\Base\BaseSocket;
use Ratchet\ConnectionInterface;
use App\GameRequisits\Users\Player;
use App\GameRequisits\Game;
use App\Socket\UserMessages\Composer;


class ChatSocket extends BaseSocket
{
    protected $clients;
    private $countOfClickingPlayers = [];

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $connection)
    {
        if (!Game::checkConnectAbility()) {
            return;
        }

        $player = new Player($connection);

        Game::addPlayer($player);
        
        $this->clients->attach($connection);
        echo "connected {$connection->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $player_sender, $msg)
    {
        $player_data = json_decode($msg, true);
        
        if (Game::checkConnectAbility()) {
            if (isset($player_data['checkConnection']) && $player_data['checkConnection']) {

                $checkConnectionData = Composer::checkConnection($player_data, $player_sender);

                $player_sender->send(json_encode($checkConnectionData));
                
            }else if (isset($player_data['readyToPlay']) && $player_data['readyToPlay']) {
                foreach (Game::getAllPlayers() as $player) {
                    if ($player->getConnection() == $player_sender) {
                        $player->readyToPlay();
                    }
                }
            
            
                if (Game::areAllPlayersReady()) {
                    foreach (Game::getAllPlayers() as $player) {
                        $dataForRoundStarting = Composer::readyToPlay($player_data, $player);
                        $player->getConnection()->send(json_encode($dataForRoundStarting));
                    }
                }
            }
            
        } else {
            //если отправитель не в списке игроков
            $playersIds = [];
            foreach (Game::getAllPlayers() as $player) {
                $playersIds[] = $player->getId();
            }
        
            if (isset($player_data['id'])) {
                if (!in_array($player_data['id'], $playersIds)) {
                    $player_sender->send(json_encode([
                        'connection_error' => true,
                        'msg' => "Раунд в процессе, Вы сможете подключиться по завершении раунда..."
                    ]));
                } else {
                    $dataForReconnecting = Composer::tryReconnect($player_data, $player_sender);
                    

                    $this->clients->attach($player_sender);

                    $dataForReconnecting['reconnectingPlayer']
                        ->getConnection()
                        ->send(json_encode($dataForReconnecting['roundStateAfterReconnect']));
                }
            }
        }
        
        if (isset($player_data['makingBet']) && $player_data['makingBet']) {
            foreach (Game::getAllPlayers() as $player) {
                if ($player->getConnection() == $player_sender) {
                    $response = $player->makeBet((int)$player_data["betSum"]);
                    
                    if (!$response) {
                        
                        $balanceError = [
                            'balanceError' => true,
                            'msg' => 'Ваша ставка превышает Ваш баланс'
                        ];

                        $player_sender->send(json_encode($balanceError));
                    }
                }
            }
            if (!Game::isCooking()) {

                $currentRound = Game::getCurrentRound();

                $dataAboutRoundState = [
                    "roundStateAfterBetting" => true,
                    "isCooking" => Game::isCooking(),
                    "currentStepPlayer" => $currentRound->getCurrentStepPlayer()->getName(),
                    "nextStepPlayer" => $currentRound->getNextStepPlayer()->getName(),
                    "playerOpenCardAbility" => $currentRound->getPlayerOpenCardAbility(),
                    "lastBet" => $currentRound->getLastBet(),
                    "playerTakingConWithoutShowingUp" => $currentRound->getPlayerTakingConWithoutShowingUp(),
                    "roundCashBox" => $currentRound->getRoundCashBox(),
                    "isRoundEndWithoutShowingUp" => $currentRound->isRoundEndWithoutShowingUp(),
                    "balanceOfAllPlayers" => Game::getBalanceOfAllPlayers(),
                    "defaultBet" => Game::getDefaultBet(),
                    "stepInBets" => Game::getStepInBets(),
                    "defaultBets" => Game::getCurrentRound()->getRoundDefaultBets(),
                    "bets" => $currentRound->getRoundBets(),
                    "winner" => $currentRound->getWinner(),
                    "savingPlayers" =>$currentRound->getSavingPlayers(),
                    "toCollate" => $currentRound->getNextStepPlayerToCollate()
                ];
                
                foreach ($this->clients as $client) {
                    $client->send(json_encode($dataAboutRoundState));
                }
            } else {
                $currentCooking = Game::getCurrentCooking();

                $dataAboutRoundState = [
                    "roundStateAfterBetting" => true,
                    "isCooking" => Game::isCooking(),
                    "currentStepPlayer" => $currentCooking->getCurrentStepPlayer()->getName(),
                    "nextStepPlayer" => $currentCooking->getNextStepPlayer()->getName(),
                    "playerOpenCardAbility" => $currentCooking->getPlayerOpenCardAbility(),
                    "lastBet" => $currentCooking->getLastBet(),
                    "playerTakingConWithoutShowingUp" => $currentCooking->getPlayerTakingConWithoutShowingUp(),
                    "roundCashBox" => $currentCooking->getCashBox(),
                    "balanceOfAllPlayers" => $currentCooking->getBalanceOfAllPlayers(),
                    "defaultBet" => Game::getDefaultBet(),
                    "stepInBets" => Game::getStepInBets(),
                    "bets" => $currentCooking->getRoundBets(),
                    "winner" => $currentCooking->getWinner(),
                    "savingPlayers" =>$currentCooking->getSavingPlayers(),
                    "toCollate" => $currentCooking->getNextStepPlayerToCollate()
                ];
                
                foreach ($this->clients as $client) {
                    $client->send(json_encode($dataAboutRoundState));
                }
            }
        } else if (isset($player_data['checkUserCardsValue']) && $player_data['checkUserCardsValue']) {

            Game::getCurrentRound()->checkUserCardsValue();
            Game::getCurrentRound()->setWinnerAfterOpeningCards();

            $dataAfterOpeningCards = [
                "dataAfterOpeningCards" => true,
                "totalCashBox" => 
                    Game::getCurrentRound()->getRoundCashBox() +
                    Game::getCurrentRound()->getSumOfDeafultBets(),
                "playersPoints" => Game::getPlayersPointsAfterOpeningCards(),
                "winnerAfterOpening" => Game::getCurrentRound()->getWinnerAfterOpeningCards(),
                "allCards" => Game::getAllPlayersCards(),
                "allPlayers" => Game::getAllPlayersNormalizedForGame(),
            ];

            foreach ($this->clients as $client) {
                $client->send(json_encode($dataAfterOpeningCards));
            }

        } else if (isset($player_data['endRoundWithoutShowingUp']) && $player_data['endRoundWithoutShowingUp']) {
            
            Game::endRoundWithoutShowingUp();

            $dataAfterEndingRoundWithoutShowingUp = [
                "nextRound" => true,
                "winner" => Game::getCurrentRound()->getWinner()->getName()
            ];
            
            foreach ($this->clients as $client) {
                $client->send(json_encode($dataAfterEndingRoundWithoutShowingUp));
            }
        } else if (isset($player_data['endRoundAfterOpeningCards']) && $player_data['endRoundAfterOpeningCards']) {
            
            Game::endRoundAfterOpeningCards();

            $dataAfterEndingRoundAfterOpeningCards = [
                "nextRound" => true,
                "winner" => Game::getCurrentRound()->getWinnerAfterOpeningCards()
            ];
            
            foreach ($this->clients as $client) {
                $client->send(json_encode($dataAfterEndingRoundAfterOpeningCards));
            }
        } else if (isset($player_data['shareCashBoxAfterOpening']) && $player_data['shareCashBoxAfterOpening']) {
            
            Game::shareCashBoxAfterOpening();

            $dataAfterEndingRoundAfterOpeningCards = [
                "nextRound" => true,
                "winner" => Game::getCurrentRound()->getWinnerAfterOpeningCards()
            ];
                
            foreach ($this->clients as $client) {
                $client->send(json_encode($dataAfterEndingRoundAfterOpeningCards));
            }

            $countOfClickingPlayers = [];
            
        } elseif (isset($player_data['aboutCooking']) && $player_data['aboutCooking']) {
            
            foreach (Game::getAllPlayers() as $player) {
                if ($player->getConnection() == $player_sender) {
                    $player->readyToCook($player_data['cooking']);
                }
            }

            if (Game::isSomeNotWinnerAgreedToCook()) {
                foreach (Game::getAllPlayers() as $player) {
                    $cardsRaw = $player->getCardsOnHand();
                    $cardsNormalizedForUser = [];

                    foreach ($cardsRaw as $card) {
                        $cardsNormalizedForUser[] = [
                            "name" => $card->getName(),
                            "suit" => $card->getSuit(),
                            "face" => $card->getFace()
                        ];
                    }
                    $someNotWinnersAgreeToCook = [
                        "someNotWinnersAgreeToCook" => true,
                        'isCooking' => Game::isCooking(),
                        'lastRoundCashBox' => Game::getLastRoundCashBox(),
                        "cards" => $cardsNormalizedForUser,
                        "name" =>$player->getName(),
                        "balance" =>$player->getBalance(),
                        "allPlayers" => Game::getAllPlayersNormalizedForGame(),
                        "allPlayersIds" => Game::getAllPlayersIdsNormalizedForGame(),
                        "currentDistributor" => Game::getCurrentDistributor()->getName(),
                        "currentFirstWordPlayer" => Game::getCurrentFirstWordPlayer()->getName(),
                        "currentRoundId" => Game::getCurrentRoundId(),
                        "stepInBets" => Game::getStepInBets(),
                        "savingPlayers" =>Game::getCurrentRound()->getSavingPlayers(),
                    ];
                    
                    $player->getConnection()->send(json_encode($someNotWinnersAgreeToCook));
                }
            } else if (Game::isAllWinnersAgreedToCook()) {
                foreach (Game::getAllPlayers() as $player) {
                    
                    $cardsRaw = $player->getCardsOnHand();
                    $cardsNormalizedForUser = [];

                    foreach ($cardsRaw as $card) {
                        $cardsNormalizedForUser[] = [
                            "name" => $card->getName(),
                            "suit" => $card->getSuit(),
                            "face" => $card->getFace()
                        ];
                    }
                    $allWinnersAgreeToCook = [
                        'allWinnersAgreeToCook' => true,
                        'winners' => Game::getLastRoundWinnerNormalized(),
                        'isCooking' => Game::isCooking(),
                        'lastRoundCashBox' => Game::getLastRoundCashBox(),
                        "cards" => $cardsNormalizedForUser,
                        "name" => $player->getName(),
                        "balance" => $player->getBalance(),
                        'allPlayers' => Game::getAllPlayersNormalizedForGame(),
                        "cookingPlayers" => Game::getCurrentCooking()->getPlayersNormalized(),
                        "allPlayersIds" => Game::getAllPlayersIdsNormalizedForGame(),
                        "currentDistributor" => Game::getCurrentDistributor()->getName(),
                        "currentFirstWordPlayer" => Game::getCurrentFirstWordPlayer()->getName(),
                        "currentCookingId" => Game::getCurrentCookingId(),
                        "stepInBets" => Game::getStepInBets(),
                        "defaultBet" => Game::getDefaultBet(),
                    ];
                    
                    $player->getConnection()->send(json_encode($allWinnersAgreeToCook));
                }
            } else if (Game::isNoneNotWinnersAgreedToCook()) {

                $noneNotWinnersAgreedToCook = [
                    'noneNotWinnersAgreedToCook' => true,
                    'winners' => Game::getCurrentRound()->getWinnerAfterOpeningCards()
                ];

                foreach ($this->clients as $client) {
                    $client->send(json_encode($noneNotWinnersAgreedToCook));
                }
            } 
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        if (Game::checkConnectAbility()) {
            Game::deletePlayerDueToDisconnect($conn);
        }

        $this->clients->detach($conn);
        echo "Client {$conn->resourceId} has been disconnected \n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "an error has occured: {$e->getMessage()}\n";
        echo $e->getTraceAsString();

        $conn->close();
    }
}

/*foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($encoded);
                var_dump($client);
            }
        }*/
/*echo sprintf(
            'Connection %d sending message "%s" to %d other connection %s' . "\n",
            $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's'
        );*/