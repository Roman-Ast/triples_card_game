<?php

namespace App\Game\Cards;

class Card
{
    private $id;
    private $name;
    private $suit;
    private $value;

    public function __construct(int $id, String $name, String $suit, int $value)
    {
        $this->id = $id;
        $this->name = $name;
        $this->suit = $suit;
        $this->value = $value;
    }

    public function getId():int
    {
        return $this->id;
    }

    public function getName():string
    {
        return $this->name;
    }

    public function getValue():int
    {
        return $this->value;
    }

    public function getSuit():string
    {
        return $this->suit;
    }

    public function becomeSuitable(array $cards) {
        foreach ($cards as $index => $card) {
            if ($card->getName() == 'Семь' && $card->getSuit() == 'Пики') {
                unset($cards[$index]);
            }
        }
        $cards = array_slice($cards, 0);
        if ($cards[0]->getName() === $cards[1]->getName()) {
            array_push(
                $cards, 
                new Card(21, $cards[0]->getName(), $cards[0]->getSuit(), $cards[0]->getValue())
            );
        } else if ($cards[0]->getSuit() === $cards[1]->getSuit()) {
            array_push(
                $cards, 
                new Card(21, "Туз", $cards[0]->getSuit(), 11)
            );
        } else {
            array_push(
                $cards, 
                new Card(21, "Туз", $cards[0]->getSuit(), 11)
            );
        }

        return $cards;
    }
}