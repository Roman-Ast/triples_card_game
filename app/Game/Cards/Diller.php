<?php

namespace App\Game\Cards;

use App\Game\Cards\Deck;
use App\Game\Game;

class Diller
{
    private const NUMBER_OF_CARDS_ON_HAND = 3;

    public static function distribute()
    {
        $deckRaw = new Deck();
        $deck = $deckRaw->getDeck();

        foreach (Game::getAllPlayers() as $player) {
            for ($i = 0; $i < self::NUMBER_OF_CARDS_ON_HAND; $i++) {
                $rand = rand(1, count($deck) - 1);
                $card = $deck[$rand];
                $player->take_card($card);
                unset($deck[$rand]);
                $deck = array_slice($deck, 0);
            }
        }
    }
}