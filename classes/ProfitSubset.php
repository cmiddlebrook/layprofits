<?php

class ProfitSubset
{
    private $profitTarget = 0;
    private $stopLoss = 0;
    private $profit = 0.0;
    private $numberOfDays = 0;

    public function __construct(int $profitTarget, int $stopLoss, float $profit, int $numberOfDays)
    {
        $this->profitTarget = $profitTarget;
        $this->stopLoss = $stopLoss;
        $this->profit = $profit;
        $this->numberOfDays = $numberOfDays;
    }

    public function getProfit()
    {
        return $this->profit();
    }
}
