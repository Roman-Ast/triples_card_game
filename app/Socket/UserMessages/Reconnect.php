<?php

namespace App\Socket\UserMessages;

class Reconnect
{
    public static function check(array $player_data, Player $player_sender)
    {
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

        return [
            'reconnectingPlayer' => $reconnectingPlayer,
            'roundStateAfterReconnect' => $roundStateAfterReconnect
        ];
    }
}