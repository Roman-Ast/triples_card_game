<?php

namespace App\Socket;

use App\Socket\Base\BaseSocket;
use Ratchet\ConnectionInterface;
use App\GameRequisits\Users\Player;
use App\GameRequisits\Game;


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
                foreach (Game::getAllPlayers() as $player) {
                    if ($player->getConnection() == $player_sender) {
                        $player->setId((int)$player_data["id"]);
                        $player->setName($player_data["name"]);
                        $player->setBalance();
                    }
                }
                $connectedPlayers = [];
                foreach (Game::getAllPlayers() as $player) {
                    $connectedPlayers[] = $player->getName();
                }
    
                $checkConnectionData = [
                    'checkConnection' => true,
                    'connectedPlayers' => $connectedPlayers
                ];
                
                
                $player_sender->send(json_encode($checkConnectionData));
                
            }else if (isset($player_data['readyToPlay']) && $player_data['readyToPlay']) {
                foreach (Game::getAllPlayers() as $player) {
                    if ($player->getConnection() == $player_sender) {
                        $player->readyToPlay();
                    }
                }
            }
            
            if (Game::areAllPlayersReady()) {
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

                    $dataForRoundStarting = [
                        "dataForRoundStarting" => true,
                        "cards" => $cardsNormalizedForUser,
                        "name" =>$player->getName(),
                        "balance" =>$player->getBalance(),
                        "allPlayers" => Game::getAllPlayersNormalizedForGame(),
                        "allPlayersIds" => Game::getAllPlayersIdsNormalizedForGame(),
                        "defaultBets" => Game::getCurrentRound()->getRoundDefaultBets(),
                        "currentDistributor" => Game::getCurrentDistributor()->getName(),
                        "currentFirstWordPlayer" => Game::getCurrentFirstWordPlayer()->getName(),
                        "currentRoundId" => Game::getCurrentRoundId(),
                        "defaultBet" => Game::getDefaultBet(),
                        "stepInBets" => Game::getStepInBets()
                    ];
                    $player->getConnection()->send(json_encode($dataForRoundStarting));
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
                    foreach (Game::getAllPlayers() as $player) {
                        if ((int)$player->getId() === (int)$player_data['id']) {
                            $player->setConnection($player_sender);
                        }
                    }

                    $reconnectingPlayer = null;
                    foreach (Game::getAllPlayers() as $player) {
                        if ($player->getConnResourceId() === $player_sender->resourceId) {
                            $reconnectingPlayer = $player;
                        }
                    }

                    $cardsNormalizedForUser = [];
                    foreach ($reconnectingPlayer->getCardsOnHand() as $card) {
                        $cardsNormalizedForUser[] = [
                            "name" => $card->getName(),
                            "suit" => $card->getSuit(),
                            "face" => $card->getFace()
                        ];
                    }

                    $currentRound = Game::getCurrentRound();

                    $roundStateAfterReconnect = [
                        "reconnect" => true,
                        "cards" => $cardsNormalizedForUser,
                        "name" =>$reconnectingPlayer->getName(),
                        "balance" =>$reconnectingPlayer->getBalance(),
                        "allPlayers" => Game::getAllPlayersNormalizedForGame(),
                        "allPlayersIds" => Game::getAllPlayersIdsNormalizedForGame(),
                        "defaultBets" => Game::getCurrentRound()->getRoundDefaultBets(),
                        "currentFirstWordPlayer" => Game::getCurrentFirstWordPlayer()->getName(),
                        "currentRoundId" => Game::getCurrentRoundId(),
                        "currentDistributor" => Game::getCurrentDistributor()->getName(),
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
                        "bets" => $currentRound->getRoundBets(),
                        "winner" => $currentRound->getWinner(),
                        "savingPlayers" =>$currentRound->getSavingPlayers(),
                        "toCollate" => $currentRound->getNextStepPlayerToCollate()
                    ];
                    $this->clients->attach($player_sender);
                    $reconnectingPlayer->getConnection()->send(json_encode($roundStateAfterReconnect));
                }
            }
        }
        
        if (isset($player_data['makingBet']) && $player_data['makingBet']) {
            /*foreach (Game::getAllPlayers() as $player) {
                var_dump('resourceId: '.$player->getConnResourceId());
                var_dump('id: '.$player->getId());
            }
            var_dump($player_sender->resourceId);*/
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

            $currentRound = Game::getCurrentRound();

            $dataAboutRoundState = [
                "roundStateAfterBetting" => true,
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
                "allCards" => Game::getAllPlayersCards()
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
            
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        var_dump(Game::getAllPlayers());
        var_dump(Game::checkConnectAbility());
        if (Game::checkConnectAbility()) {
            Game::deletePlayerDueToDisconnect($conn);
            var_dump(Game::getAllPlayers());
        }
        var_dump($this->clients);
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