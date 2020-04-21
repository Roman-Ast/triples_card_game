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
}