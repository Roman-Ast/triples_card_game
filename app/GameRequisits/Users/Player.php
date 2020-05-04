<?php

namespace App\GameRequisits\Users;

use App\GameRequisits\Game;
use App\GameRequisits\Cards\Card;
use Ratchet\ConnectionInterface;
use App\User;

class Player
{
    private $id;
    private $conn;
    private $name;
    private $cards_on_hand = [];
    private $balance;
    private $radiness = false;
    private $distributor = false;
    private $cardsValueAfterOpening = 0;
    private $lastRaiseOrColl = 0;

    public function __construct(ConnectionInterface $conn)
    {
        $this->conn = $conn;
    }

    public function setLastRaiseOrColl(int $bet)
    {
        $this->lastRaiseOrColl += $bet;
    }

    public function getLastRaiseOrColl()
    {
        return $this->lastRaiseOrColl ?? null;
    }

    public function makeDefaultBet(int $defaultBet)
    {
        $this->balance -= $defaultBet;
        return $defaultBet;
    }

    public function makeBet(int $bet)
    {
        if ($this->balance - $bet < 0) {
            return 'false';
        }
        
        $this->balance -= $bet;
        Game::getCurrentRound()->takeBet($this, $bet);

        return 'Ok';
    }

    public function save()
    {

    }

    public function readyToPlay()
    {
        $this->radiness = true;
        Game::SayReadyToGame();
    }

    public function changeRadinessAfterEndingRound()
    {
        $this->radiness = false;
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

    public function setConnection(ConnectionInterface $conn)
    {
        $this->conn = $conn;
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
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setBalance()
    {
        $userFromDB = User::find($this->id);
        $this->balance = $userFromDB->balance;
    }

    public function getCardsValueAfterOpening()
    {
        return $this->cardsValueAfterOpening;
    }

    public function setCardsValueAfterOpening(int $cardsValue)
    {
        $this->cardsValueAfterOpening = $cardsValue;
    }

    public function dropCards()
    {
        $this->cards_on_hand = [];
    }

}