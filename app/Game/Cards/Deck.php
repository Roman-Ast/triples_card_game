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
            new Card(1, "Туз", "Червы", 11),
            new Card(2, "Туз", "Буби", 11),
            new Card(3, "Туз", "Крести", 11),
            new Card(4, "Туз", "Пики", 11),
            new Card(5, "Король", "Червы", 11),
            new Card(6, "Король", "Буби", 11),
            new Card(7, "Король", "Крести", 11),
            new Card(8, "Король", "Пики", 11),
            new Card(9, "Дама", "Червы", 11),
            new Card(10, "Дама", "Буби", 11),
            new Card(11, "Дама", "Крести", 11),
            new Card(12, "Дама", "Пики", 11),
            new Card(13, "Валет", "Червы", 11),
            new Card(14, "Валет", "Буби", 11),
            new Card(15, "Валет", "Крести", 11),
            new Card(16, "Валет", "Пики", 11),
            new Card(17, "Десять", "Червы", 11),
            new Card(18, "Десять", "Буби", 11),
            new Card(19, "Десять", "Крести", 11),
            new Card(20, "Десять", "Пики", 11),
            new Card(21, "Семь", "Пики", 11),
        ];
    }

    public function getDeck()
    {
        return $this->deck;
    }
}