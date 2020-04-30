<?php

namespace App\Socket;

use App\Socket\Base\BaseSocket;
use Ratchet\ConnectionInterface;
use App\Game\Users\Player;
use App\Game\Game;


class ChatSocket extends BaseSocket
{
    protected $clients;

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
                
            if (isset($player_data['readyToPlay']) && $player_data['readyToPlay']) {
                foreach (Game::getAllPlayers() as $player) {
                    if ($player->getConnection() == $player_sender) {
                        $player->setId((int)$player_data["id"]);
                        $player->setName($player_data["name"]);
                        $player->setBalance((int)$player_data["balance"]);
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
                        
                    ];
                    $player->getConnection()->send(json_encode($dataForRoundStarting));
                }
            }
            
        } 
        
        if (isset($player_data['makingBet']) && $player_data['makingBet']) {
            
            foreach (Game::getAllPlayers() as $player) {
                if ($player->getConnection() == $player_sender) {
                    $player->makeBet((int)$player_data["betSum"]);
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
                "defaultBets" => Game::getCurrentRound()->getRoundDefaultBets(),
                "bets" => $currentRound->getRoundBets(),
                "winner" => $currentRound->getWinner(),
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
                "playersPoints" => Game::getPlyersPointsAfterOpeningCards(),
                "winnerAfterOpening" => Game::getCurrentRound()->getWinnerAfterOpeningCards()
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
        }
        
        
        
        
    }

    public function onClose(ConnectionInterface $conn)
    {
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