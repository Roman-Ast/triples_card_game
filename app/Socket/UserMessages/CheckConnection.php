<?php

namespace App\Socket\UserMessages;
use App\GameRequisits\Game;
use Ratchet\ConnectionInterface;


class CheckConnection
{
    public static function check(array $player_data, ConnectionInterface $player_sender)
    {
        foreach (Game::getAllPlayers() as $player) {
            if ($player_data['isAdmin']) {
                Game::setAdminConnection($player_sender);
                Game::deleteAdminFromGame($player_data['id']);
            }
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
            'connectedPlayers' => $connectedPlayers,
            'adminConnection' => Game::getAdminConnection()
        ];

        return $checkConnectionData;
    }
}