<?php

class ProfitAnalyser
{
    private $dbTables = array();
    private $daysProfits = array();
    private $profitSubsets = array();

    public function __construct()
    {
        $this->dbTables[0] = 'napchecker';
        $this->dbTables[1] = 'horseracing_us';
        $this->dbTables[2] = 'horseracing_aus';
        $this->dbTables[3] = 'greyhounds_aus';
    }

    private function getProfitByRace(int $strategyNumber)
    {
        $db = Database::getInstance();
        $tableName = $this->getTableName($strategyNumber);

        $sql = "SELECT start, track, sum(profit) as race_profit FROM $tableName GROUP BY start, track;";

        $statement = $db->prepare($sql);
        $statement->execute();

        return $statement;
    }

    private function getTableName(int $strategyNumber)
    {
        if (!array_key_exists($strategyNumber-1, $this->dbTables))
        {
            throw new RuntimeException("Unknown strategy number: $strategyNumber");
        }

        return $this->dbTables[$strategyNumber-1];
    }

    public function analyse(int $strategyNumber)
    {
        $raceProfits = $this->getProfitByRace($strategyNumber);
        $this->analyseProfitForStrategy($raceProfits);
    }

    private function analyseProfitForStrategy(PDOStatement $raceProfits)
    {
        // store the profits for each race in an array indexed by the date run
        foreach($raceProfits as $row)
        {
            $this->recordProfitByDay($row);
        }

        // now analyse on a per-day basis
        $this->analyseProfitOverRangeOfTargets();

        // print the best results
        ksort($this->profitSubsets);
        $numSubsets = count($this->profitSubsets);
        $mostProfitableSubsets = array_slice($this->profitSubsets, $numSubsets-5);
        foreach($mostProfitableSubsets as $subset)
        {
            $subset->print();
        }
    }

    private function analyseProfitOverRangeOfTargets()
    {
        // try 10,000 combinations of profit and loss targets to find the most profitable combination
        $count = 0;
        for($profitTarget = 1; $profitTarget <= 100; $profitTarget += 1)
        {
            for($stopLoss = -100; $stopLoss <= -1; $stopLoss += 1)
            {
                $profitSubset = new ProfitSubset($profitTarget, $stopLoss);
                $numberOfDays = 0;
                $totalProfit = 0.0;
                foreach ($this->daysProfits as $day => $profits)
                {
                    $totalProfit += $this->analyseDailyProfit($day, $profitSubset);
                    $numberOfDays++;
                }

                // only keep the profitable subsets
                if ($totalProfit > 0.0)
                {
                    $count++;
                    $this->profitSubsets[$totalProfit] = $profitSubset;
                }
            }
        }
        echo count($this->profitSubsets) . "subsets analysed\n";
    }

    private function analyseDailyProfit(string $startString, ProfitSubset $profitSubset)
    {
        $profitTarget = $profitSubset->getProfitTarget();
        $stopLoss = $profitSubset->getStopLoss();

        // $nfmt = new NumberFormatter("en", NumberFormatter::CURRENCY);
        $runningTotal = 0.0;
        $raceProfits = $this->daysProfits[$startString];
        foreach ($raceProfits as $raceProfit)
        {
            $profit = round($raceProfit['race_profit'],2);
            $runningTotal += $profit;

            if ($runningTotal >= $profitTarget)
            {
                break;
            }

            if ($runningTotal <= $stopLoss)
            {
                break;
            }
        }

        $profitSubset->addDaysProfit($runningTotal);
        return $runningTotal;
    }

    private function recordProfitByDay($raceProfit)
    {
        $start = new DateTime($raceProfit['start']);
        $start->setTime(0,0);
        $startString = $start->format('d-M-Y');

        if (!array_key_exists($startString, $this->daysProfits))
        {
            $this->daysProfits[$startString] = array();
        }

        array_push($this->daysProfits[$startString], $raceProfit);
    }


}
