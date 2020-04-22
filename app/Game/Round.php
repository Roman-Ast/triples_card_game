<?php

namespace App\Game;

use App\Game\Cards\Diller;
use App\Game\Game;

class Round
{
    private $id;
    private const DEFAULT_BET = 50;
    private $summ_of_default_bets = [];
    private $summ_of_all_bets = [];

    public function __construct(int $currentRound)
    {
        $this->id = $currentRound;
    }
    public function start()
    {
        Diller::distribute();
        $this->takeDefaultBet();
    }

    private function takeDefaultBet()
    {
        foreach (Game::getAllPlayers() as $player) {
            //var_dump($player);
            $this->summ_of_default_bets[] = [
                "betMaker" => $player->getName(),
                "defaultBet" => $player->makeDefaultBet(self::DEFAULT_BET)
            ];
        }
    }

    public function getRoundDefaultBets()
    {
        return $this->summ_of_default_bets;
    }
}