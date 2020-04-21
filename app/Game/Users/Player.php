<?php

namespace App\Game\Users;

use App\Game\Game;
use App\Game\Cards\Card;
use Ratchet\ConnectionInterface;

class Player
{
    private $id;
    private $conn;
    private $name;
    private $cards_on_hand = [];
    private $money_on_hand;
    private $radiness = false;

    public function __construct(ConnectionInterface $conn)
    {
        $this->conn = $conn;
    }

    public function make_bet(int $bet)
    {

    }

    public function save()
    {

    }

    public function readyToPlay()
    {
        $this->radiness = true;
        Game::SayReadyToGame();
    }

    public function getRadiness()
    {
        return $this->radiness;
    }
    public function take_card(Card $card)
    {
        $this->cards_on_hand[] = $card;
    }

    public function getConnResourceId()
    {
        return $this->conn->resourceId;
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function getCardsOnHand()
    {
        return $this->cards_on_hand;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getBill()
    {
        return $this->money_on_hand;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setBill(int $money)
    {
        $this->money_on_hand = $money;
    }

}