<?php

namespace App\Game\Cards;

use App\Game\Cards\Card;
use App\Socket\ChatSocket;
use App\Game\Game;

class Deck
{
    private $deck;

    public function __construct()
    {
        $this->deck = [
            new Card(1, "Туз", "Червы", 11, '/images/cards/ace-hearts.jpg'),
            new Card(2, "Туз", "Буби", 11, '/images/cards/ace-bubi.jpg'),
            new Card(3, "Туз", "Крести", 11, '/images/cards/ace-clubs.jpg'),
            new Card(4, "Туз", "Пики", 11, '/images/cards/ace-piki.jpg'),
            new Card(5, "Король", "Червы", 10, '/images/cards/king-hearts.jpg'),
            new Card(6, "Король", "Буби", 10, '/images/cards/king-bubi.jpg'),
            new Card(7, "Король", "Крести", 10, '/images/cards/king-clubs.jpg'),
            new Card(8, "Король", "Пики", 10, '/images/cards/king-piki.jpg'),
            new Card(9, "Дама", "Червы", 10, '/images/cards/lady-hearts.jpg'),
            new Card(10, "Дама", "Буби", 10, '/images/cards/lady-bubi.jpg'),
            new Card(11, "Дама", "Крести", 10, '/images/cards/lady-clubs.jpg'),
            new Card(12, "Дама", "Пики", 10, '/images/cards/lady-piki.jpg'),
            new Card(13, "Валет", "Червы", 10, '/images/cards/valet-hearts.jpg'),
            new Card(14, "Валет", "Буби", 10, '/images/cards/valet-bubi.jpg'),
            new Card(15, "Валет", "Крести", 10, '/images/cards/valet-clubs.jpg'),
            new Card(16, "Валет", "Пики", 10, '/images/cards/valet-piki.jpg'),
            new Card(17, "Десять", "Червы", 10, '/images/cards/10-hearts.jpg'),
            new Card(18, "Десять", "Буби", 10, '/images/cards/10-bubi.jpg'),
            new Card(19, "Десять", "Крести", 10, '/images/cards/10-clubs.jpg'),
            new Card(20, "Десять", "Пики", 10, '/images/cards/10-piki.jpg'),
            new Card(21, "Семь", "Пики", 11, '/images/cards/joker.jpg'),
        ];
    }

    public function getDeck()
    {
        return $this->deck;
    }
}