<?php

class BettingStrategyFactory
{
    private $strategies = array();

    public function __construct()
    {
        $this->strategies[0] = 'Napchecker';
        $this->strategies[1] = 'HorseRacingUS';
        $this->strategies[2] = 'HorseRacingAUS';
        $this->strategies[3] = 'GreyhoundsAUS';
    }

    private function getClassName($strategyName)
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
        $className = $this->getClassName($strategyName);

        // instantiate the correct class and parse the bet data
        $strategy = new $className;
        $strategy->parseBet($data);
    }

    public function analyse(int $strategyNr)
    {
        $strategyName = $this->strategies[$strategyNr-1];
        $className = $this->getClassName($strategyName);
        $strategy = new $className;
        $strategy->analyse();
    }
}
