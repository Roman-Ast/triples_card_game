<?php

namespace App\GameRequisits\Cards;

use App\GameRequisits\Cards\Card;
use App\Socket\ChatSocket;
use App\GameRequisits\Game;

class Deck
{
    private $deck;

    public function __construct()
    {
        $this->deck = [
            new Card(1, "Туз", "Червы", 11, '/images/cards/ace-hearts.png'),
            new Card(2, "Туз", "Буби", 11, '/images/cards/ace-bubi.png'),
            new Card(3, "Туз", "Крести", 11, '/images/cards/ace-clubs.png'),
            new Card(4, "Туз", "Пики", 11, '/images/cards/ace-piki.png'),
            new Card(5, "Король", "Червы", 10, '/images/cards/king-hearts.png'),
            new Card(6, "Король", "Буби", 10, '/images/cards/king-bubi.png'),
            new Card(7, "Король", "Крести", 10, '/images/cards/king-clubs.png'),
            new Card(8, "Король", "Пики", 10, '/images/cards/king-piki.png'),
            new Card(9, "Дама", "Червы", 10, '/images/cards/lady-hearts.png'),
            new Card(10, "Дама", "Буби", 10, '/images/cards/lady-bubi.png'),
            new Card(11, "Дама", "Крести", 10, '/images/cards/lady-clubs.png'),
            new Card(12, "Дама", "Пики", 10, '/images/cards/lady-piki.png'),
            new Card(13, "Валет", "Червы", 10, '/images/cards/valet-hearts.png'),
            new Card(14, "Валет", "Буби", 10, '/images/cards/valet-bubi.png'),
            new Card(15, "Валет", "Крести", 10, '/images/cards/valet-clubs.png'),
            new Card(16, "Валет", "Пики", 10, '/images/cards/valet-piki.png'),
            new Card(17, "Десять", "Червы", 10, '/images/cards/10-hearts.png'),
            new Card(18, "Десять", "Буби", 10, '/images/cards/10-bubi.png'),
            new Card(19, "Десять", "Крести", 10, '/images/cards/10-clubs.png'),
            new Card(20, "Десять", "Пики", 10, '/images/cards/10-piki.png'),
            new Card(21, "Семь", "Пики", 11, '/images/cards/joker.png'),
        ];
    }

    public function getDeck()
    {
        return $this->deck;
    }
}