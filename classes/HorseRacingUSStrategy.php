<?php

class HorseRacingUSStrategy extends BettingStrategy
{
    protected $tableName = "horseracing_us";
    
    protected function extractRunner(string $selection)
    {
        $this->runner = $selection;
    }
}
