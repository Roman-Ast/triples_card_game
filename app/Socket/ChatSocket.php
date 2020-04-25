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
                
            if ($player_data['readyToPlay']) {
                foreach (Game::getAllPlayers() as $player) {
                    if ($player->getConnection() == $player_sender) {
                        $player->setId($player_data["id"]);
                        $player->setName($player_data["name"]);
                        $player->setBalance($player_data["balance"]);
                        $player->readyToPlay();
                    }
                }
            }
            //var_dump(Game::getAllPlayers());
            if (Game::areAllPlayersReady()) {
                foreach (Game::getAllPlayers() as $player) {
                    $cardsRaw = $player->getCardsOnHand();
                    $cardsNormalizedForUser = [];

                    foreach ($cardsRaw as $card) {
                        $cardsNormalizedForUser[] = [
                            "name" => $card->getName(),
                            "suit" => $card->getSuit()
                        ];
                    }

                    $dataForRoundStarting = [
                        "dataForRoundStarting" => true,
                        "cards" => $cardsNormalizedForUser,
                        "name" =>$player->getName(),
                        "balance" =>$player->getBalance(),
                        "allPlayers" => Game::getAllPlayersNormalizedForGame(),
                        "defaultBets" => Game::getRounds()[Game::getCurrentRoundId()]->getRoundDefaultBets(),
                        "currentDistributor" => Game::getCurrentDistributor()->getName(),
                        "currentFirstWordPlayer" => Game::getCurrentFirstWordPlayer()->getName(),
                        "currentRoundId" => Game::getCurrentRoundId(),
                        "defaultBet" => Game::getDefaultBet()
                    ];
                    $player->getConnection()->send(json_encode($dataForRoundStarting));
                }
            }
            
        } else if (isset($player_data['readyToPlay']) && $player_data['readyToPlay'] && !$player_data['makingBet']) {
            $error = ["alreadyRunningGame" => "Раунд уже начался, подождите пока раунд не закончится..."];
            $player_sender->send(json_encode($error));
        } elseif ($player_data['makingBet']) {
            
            foreach (Game::getAllPlayers() as $player) {
                if ($player->getConnection() == $player_sender) {
                    $player->makeBet((int)$player_data["betSum"]);
                }
            }

            $dataAboutRoundState = [
                "roundStateAfterBetting" => true,
                "currentStepPlayer" => Game::getCurrentRound()->getCurrentStepPlayer()->getName(),
                "nextStepPlayer" => Game::getCurrentRound()->getNextStepPlayer()->getName(),
                "playerOpenCardAbility" => Game::getCurrentRound()->getPlayerOpenCardAbility(),
                "lastBet" => Game::getCurrentRound()->getLastBet(),
                "playerTakingConWithoutShowingUp" => Game::getCurrentRound()->getPlayerTakingConWithoutShowingUp(),
                "roundCashBox" => Game::getCurrentRound()->getRoundCashBox()
            ];

            foreach ($this->clients as $client) {
                $client->send(json_encode($dataAboutRoundState));
            }
        } elseif ($player_data['endRoundWithoutShowingUp']) {
            Game::endRoundWithoutShowingUp();
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

    public static function getAllPlayers()
    {
        return $this->clients;
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