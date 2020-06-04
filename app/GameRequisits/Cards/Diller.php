<?php

namespace App\GameRequisits\Cards;

use App\GameRequisits\Cards\Deck;
use App\GameRequisits\Game;

class Diller
{
    private const NUMBER_OF_CARDS_ON_HAND = 3;

    public static function distribute(array $players)
    {
        $deckRaw = new Deck();
        $deck = $deckRaw->getDeck();
        
        shuffle($deck);
        
        foreach ($players as $player) {
            /*if ($player->getName() === 'Рус') {
                $player->takeCard(new Card(5, "Король", "Червы", 10, '/images/cards/king-hearts.png'));
                $player->takeCard(new Card(13, "Валет", "Червы", 10, '/images/cards/valet-hearts.png'));
                $player->takeCard(new Card(18, "Десять", "Буби", 10, '/images/cards/10-bubi.png'));
            } else if ($player->getName() === 'Санечка') {
                $player->takeCard(new Card(15, "Валет", "Крести", 10, '/images/cards/valet-clubs.png'));
                $player->takeCard(new Card(11, "Дама", "Крести", 10, '/images/cards/lady-clubs.png'));
                $player->takeCard(new Card(18, "Десять", "Пики", 10, '/images/cards/10-bubi.png'));
            }
            else if ($player->getName() === 'Роман') {
                $player->takeCard(new Card(0, "Король", "Пики", 10, '/images/cards/king-piki.png'));
                $player->takeCard(new Card(0, "Туз", "Червы", 11, '/images/cards/ace-hearts.png'));
                $player->takeCard(new Card(0, "Десять", "Крести", 10, '/images/cards/10-clubs.png'));
            }
            else if ($player->getName() === 'Оксана') {
                $player->takeCard(new Card(15, "Валет", "Пики", 10, '/images/cards/valet-clubs.png'));
                $player->takeCard(new Card(11, "Дама", "Червы", 10, '/images/cards/lady-clubs.png'));
                $player->takeCard(new Card(18, "Десять", "Буби", 10, '/images/cards/10-bubi.png'));
            } else {*/
                for ($i = 0; $i < self::NUMBER_OF_CARDS_ON_HAND; $i++) {
                    //$rand = random_int(0, count($deck) - 1);
                    $card = $deck[$i];
                    $player->takeCard($card);
                    unset($deck[$i]);
                    $deck = array_slice($deck, 0);
                }
            //}
            
        }
    }

    public static function checkUserCardsValue(array $players)
    {
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
                    $player->setCardsValueAfterOpening($isThreeSuitSame);
                    $cardsWithProcessedSeven = null;
                    continue;
                }
                $isTwoSuitSame = self::checkForTwoSameSuit($cardsWithProcessedSeven);
                if ($isTwoSuitSame) {
                    $player->setCardsValueAfterOpening($isTwoSuitSame);
                    $cardsWithProcessedSeven = null;
                    continue;
                }
                $isOneSuit = self::checkOneSuit($cardsWithProcessedSeven);
                if ($isOneSuit) {
                    $player->setCardsValueAfterOpening($isOneSuit);
                    $cardsWithProcessedSeven = null;
                    continue;
                }
            } else {
                
                $isTriples = self::checkForTriples($cards);
                if ($isTriples) {
                    $player->setCardsValueAfterOpening($isTriples);
                continue;
                }
                $isThreeSuitSame = self::checkForThreeSameSuit($cards);
                if ($isThreeSuitSame) {
                    $player->setCardsValueAfterOpening($isThreeSuitSame);
                    continue;;
                }
                $isTwoAces = self::checkTwoAces($cards);
                if ($isTwoAces) {
                    $player->setCardsValueAfterOpening($isTwoAces);
                    continue;
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

    private static function checkTwoAces(array $cards) {
        $aces = 0;

        foreach ($cards as $card) {
            if ($card->getName() === 'Туз') {
                $aces += 1;
            }
        }

        return $aces < 2 ? false : 22;
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