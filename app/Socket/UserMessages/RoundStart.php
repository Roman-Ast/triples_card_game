<?php

namespace App\Socket\UserMessages;

use App\GameRequisits\Users\Player;
use App\GameRequisits\Game;

class RoundStart
{
    public static function ready(array $player_data, Player $player)
    {
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
        
        return $dataForRoundStarting;
    }

    public static function infoForAdmin()
    {
        $dataForRoundStarting = [
            "dataForRoundStarting" => true,
            'cards' => [],
            "allPlayers" => Game::getAllPlayersNormalizedForGame(),
            "allPlayersIds" => Game::getAllPlayersIdsNormalizedForGame(),
            "defaultBets" => Game::getCurrentRound()->getRoundDefaultBets(),
            "currentDistributor" => Game::getCurrentDistributor()->getName(),
            "currentFirstWordPlayer" => Game::getCurrentFirstWordPlayer()->getName(),
            "currentRoundId" => Game::getCurrentRoundId(),
            "defaultBet" => Game::getDefaultBet(),
            "stepInBets" => Game::getStepInBets()
        ];
        
        return $dataForRoundStarting;
    }
}