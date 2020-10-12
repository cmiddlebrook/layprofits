<?php

class HorseRacingUSStrategy extends BettingStrategy
{
    protected function extractRunner(string $selection)
    {
        $this->runner = $selection;
    }
}
