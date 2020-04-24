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
    private $balance;
    private $radiness = false;
    private $distributor = false;

    public function __construct(ConnectionInterface $conn)
    {
        $this->conn = $conn;
    }

    public function makeDefaultBet(int $defaultBet)
    {
        $this->balance -= $defaultBet;
        return $defaultBet;
    }

    public function makeBet(int $bet)
    {
        Game::currentRoundMakeBet($bet, $this->name, $this);
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

    public function getBalance()
    {
        return $this->balance;
    }

    public function setName(string $name)
    {
        $this->name = $name;
        //var_dump($this->name);
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setBalance(int $balance)
    {
        $this->balance = $balance;
    }

    public function isDistributor()
    {
        
    }

}