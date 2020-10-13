<?php

class BettingStrategyFactory
{
    private function getClassByName(string $strategyName)
    {
        $className = $strategyName . 'Strategy';

        if(!class_exists($className))
        {
            throw new RuntimeException("Unknown Betting Strategy: $strategyName");
        }

        return $className;
    }

    public function parseBet(array $data)
    {
        $strategyName = trim($data[0]);

        // skip the header row
        if ($strategyName == 'Strategy') return;

        // determine the correct class to instantiate based on the strategy
        $className = $this->getClassByName($strategyName);

        // instantiate the correct class and parse the bet data
        $strategy = new $className;
        $strategy->parseBet($data);
    }
}
