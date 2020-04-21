<?php

namespace App\Game;

use App\Game\Cards\Diller;

class Round
{
    public function start()
    {
        Diller::distribute();
    }
}