<?php

class ProfitAnalyser
{
    private $dbTables = array();
    private $daysProfits = array();
    private $highestProfit = 0.0;
    private $bestSubset = null;

    public function __construct()
    {
        $this->dbTables[0] = 'napchecker';
        $this->dbTables[1] = 'horseracing_us';
        $this->dbTables[2] = 'horseracing_aus';
        $this->dbTables[3] = 'greyhounds_aus';
    }

    private function getProfitByRace(int $strategyNumber, int $odds)
    {
        $db = Database::getInstance();
        $tableName = $this->getTableName($strategyNumber);

        $sql = "SELECT start, track, sum(profit) as race_profit FROM $tableName WHERE odds <= ? GROUP BY start, track;";

        // HACK for the US strategy, offset the time by 8 hours to bring the results of a US day into a UK day
        if($strategyNumber == 2)
        {
            $sql = "SELECT DATE_SUB(start, INTERVAL '0-8' DAY_HOUR) as start, track, sum(profit) as race_profit FROM $tableName WHERE odds <= ? GROUP BY start, track;";
        }

        $statement = $db->prepare($sql);
        $statement->execute([$odds]);
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
        // HACK I should work this into a strategy class somehow. Max odds for Napchecker is 50, all others are 21
        $maxOdds = $strategyNumber == 1 ? 40 : 21;
        for ($odds = 5; $odds <= $maxOdds; $odds++)
        {
            $raceProfits = $this->getProfitByRace($strategyNumber, $odds);
            $this->analyseProfitForStrategy($raceProfits, $odds);
        }

        echo "Best subset found: \n";
        $this->bestSubset->print();
    }

    private function analyseProfitForStrategy(PDOStatement $raceProfits, int $odds)
    {
        // store the profits for each race in an array indexed by the date run
        $this->daysProfits = array();
        foreach($raceProfits as $row)
        {
            $this->recordProfitByDay($row);
        }

        // now analyse on a per-day basis
        $this->analyseProfitOverRangeOfTargets($odds);
    }

    private function analyseProfitOverRangeOfTargets(int $odds)
    {
        // try all the combinations of profit and loss targets to find the most profitable combination
        for($profitTarget = 1; $profitTarget <= 100; $profitTarget += 1)
        {
            for($stopLoss = -1; $stopLoss >= -100; $stopLoss -= 1)
            {
                $profitSubset = new ProfitSubset($profitTarget, $stopLoss, $odds);
                $numberOfDays = 0;
                $totalProfit = 0.0;
                foreach ($this->daysProfits as $day => $profits)
                {
                    $totalProfit += $this->analyseDailyProfit($day, $profitSubset);
                }

                // is this a better subset than we have already found?
                $strikeRate = $profitSubset->getStrikeRate();
                if ($totalProfit > $this->highestProfit && $strikeRate >= 70 && $strikeRate <= 90)
                {
                    echo "New High! Profit: $totalProfit, MO: $odds, PT: $profitTarget, SL: $stopLoss, SR $strikeRate\n";
                    $this->bestSubset = $profitSubset;
                    $this->highestProfit = $totalProfit;
                }
            }
        }
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
