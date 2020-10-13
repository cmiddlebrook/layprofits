<?php

class ProfitSubset
{
    private $profitTarget = 0;
    private $stopLoss = 0;
    private $profitsByDay = array();
    private $totalProfit = 0.0;
    private $highestLoss = 0.0;
    private $numberOfProfitableDays = 0;

    public function __construct(int $profitTarget, int $stopLoss)
    {
        $this->profitTarget = $profitTarget;
        $this->stopLoss = $stopLoss;
    }

    public function getProfitTarget()
    {
        return $this->profitTarget;
    }

    public function getStopLoss()
    {
        return $this->stopLoss;
    }

    public function getProfit()
    {
        return $this->profit();
    }

    public function addDaysProfit(float $profit)
    {
        array_push($this->profitsByDay, $profit);
        $this->totalProfit += $profit;

        if ($profit < $this->highestLoss)
        {
            $this->highestLoss = $profit;
        }

        if ($profit > 0)
        {
            $this->numberOfProfitableDays++;
        }
    }

    public function print()
    {
        $numDays = count($this->profitsByDay);
        $percentageProfitable = 100 / $numDays * $this->numberOfProfitableDays;
        echo "Profit Target: $this->profitTarget, Stop Loss: $this->stopLoss, Profit: $this->totalProfit\n";
        echo "Highest Loss: $this->highestLoss, Days Analysed: $numDays, Profitable: $this->numberOfProfitableDays ($percentageProfitable%)\n";
        print_r($this->profitsByDay);
    }
}
