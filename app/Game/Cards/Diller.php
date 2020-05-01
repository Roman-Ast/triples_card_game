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
                $rand = rand(0, count($deck) - 1);
                $card = $deck[$rand];
                $player->take_card($card);
                unset($deck[$rand]);
                $deck = array_slice($deck, 0);
            }
        }
    }

    public static function checkUserCardsValue()
    {
        $players = Game::getAllPlayers();

        foreach ($players as $player) {
            $cards = $player->getCardsOnHand();
            
            foreach ($cards as $index => $card) {
                if ($card->getName() == 'Семь' && $card->getSuit() == 'Пики') {
                    $cardsWithProcessedSeven = $card->becomeSuitable($cards);
                }
            }
            
            if (isset($cardsWithProcessedSeven)) {
                
                $isTriples = self::checkForTriples($cardsWithProcessedSeven);
                if ($isTriples) {
                    $player->setCardsValueAfterOpening($isTriples);
                    $cardsWithProcessedSeven = null;
                    continue;
                }
                $isThreeSuitSame = self::checkForThreeSameSuit($cardsWithProcessedSeven);
                if ($isThreeSuitSame) {
                    var_dump($isThreeSuitSame);
                    $player->setCardsValueAfterOpening($isThreeSuitSame);
                    $cardsWithProcessedSeven = null;
                    continue;
                }
                $isTwoSuitSame = self::checkForTwoSameSuit($cardsWithProcessedSeven);
                if ($isTwoSuitSame) {
                    var_dump($isTwoSuitSame);
                    $player->setCardsValueAfterOpening($isTwoSuitSame);
                    $cardsWithProcessedSeven = null;
                    continue;
                }
                $isOneSuit = self::checkOneSuit($cardsWithProcessedSeven);
                if ($isOneSuit) {
                    var_dump($isOneSuit);
                    $player->setCardsValueAfterOpening($isOneSuit);
                    $cardsWithProcessedSeven = null;
                    continue;
                }
            } else {
                
                $isTriples = self::checkForTriples($cards);
                if ($isTriples) {
                    var_dump($isTriples);
                    $player->setCardsValueAfterOpening($isTriples);
                continue;
                }
                $isThreeSuitSame = self::checkForThreeSameSuit($cards);
                if ($isThreeSuitSame) {
                    $player->setCardsValueAfterOpening($isThreeSuitSame);
                    continue;;
                }
                $isTwoSuitSame = self::checkForTwoSameSuit($cards);
                if ($isTwoSuitSame) {
                    $player->setCardsValueAfterOpening($isTwoSuitSame);
                    continue;
                }
                $isOneSuit = self::checkOneSuit($cards);
                if ($isOneSuit) {
                    $player->setCardsValueAfterOpening($isOneSuit);
                    continue;
                }
            }
        }
    }

    private static function checkForTriples(array $cards)
    {
        $standardCardName = $cards[0]->getName();
        $isTriples = \collect($cards)->every(function($item, $key) use ($standardCardName) {
            return $item->getName() === $standardCardName;
        });
        
        if ($isTriples) {
            if ($cards[0]->getName() === 'Туз') {
                return 37;
            }
            if ($cards[0]->getName() === 'Король') {
                return 36;
            }
            if ($cards[0]->getName() === 'Дама') {
                return 35;
            }
            if ($cards[0]->getName() === 'Валет') {
                return 34;
            }
            if ($cards[0]->getName() === 'Десять') {
                return 33;
            }
        }

        return false;
    }

    private static function checkForThreeSameSuit(array $cards) {
        $standardCardSuit= $cards[0]->getSuit();
        $isSameThreeSuit = \collect($cards)->every(function($item, $key) use($standardCardSuit) {
            return $item->getSuit() === $standardCardSuit;
        });

        if ($isSameThreeSuit) {
            $totalPoints = 0;

            foreach ($cards as $card) {
                $totalPoints += $card->getValue();
            }

            return $totalPoints;
        }

        return false;
    }

    private static function checkForTwoSameSuit(array $cards) {
        $cardsSuits = array_map(function($cardObj) {
            return $cardObj->getSuit();
        }, $cards);

        $matches = array_count_values($cardsSuits);

        $twoSameSuit = false;

        foreach ($matches as $suit => $count) {
            if ($count == 2) {
                $twoSameSuit = $suit;
            }
        }
        
        if ($twoSameSuit) {
            $totalPoints = 0;

            foreach ($cards as $card) {
                if ($card->getSuit() == $twoSameSuit) {
                    $totalPoints += $card->getValue();
                }
            }

            return $totalPoints;
        }
        
        return false;
    }

    private static function checkOneSuit(array $cards) {
        $firstCardValue = $cards[0]->getValue();

        foreach ($cards as $card) {
            if ($card->getvalue() > $firstCardValue) {
                $firstCardValue = $card->getValue();
            }
        }

        return $firstCardValue;
    }
}