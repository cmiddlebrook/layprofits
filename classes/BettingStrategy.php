<?php

/*  The base BettingStrategy class has default code for US & Australian markets
    Derived classes can override any methods where the data is structured differently

    NEXT: The code from the old BetParser has now been replaced by Polymorphic classes.
    The string manipulation is all done (I think!) so the next step is the database code.
    I'll need to create a database and a table for each strategy and then write the code
    to store the imported data in those tables.
*/

class BettingStrategy
{
    protected $start;
    protected $track;
    protected $length;
    protected $class;
    protected $runner;
    protected $stake;
    protected $odds;
    protected $profit;
    protected $tableName;

    public function parseBet(array $data)
    {
        $raceFields = explode("\\", $data[1]);
        $eventName = $raceFields[0];
        $eventFields = explode(" ", $eventName);
        $raceClass = $raceFields[1];
        $classFields = explode(" ", $raceClass);
        $this->track = $eventFields[1];
        $this->stake = floatval($data[4]);
        $this->odds = floatval($data[5]);
        $this->profit = floatval($data[7]);

        $this->extractLength($classFields);
        $this->extractClass($classFields);
        $this->extractStart($eventFields);
        $this->extractRunner($data[2]);

        $this->store();
    }

    protected function extractLength(array $classFields)
    {
        $this->length = $classFields[1];
    }

    protected function extractClass(array $classFields)
    {
        $classPieces = array_slice($classFields, 2);
        $this->class = implode(" ", $classPieces);
    }

    protected function extractStart(array $eventFields)
    {
        $time = $eventFields[0];
        $day = $eventFields[3];
        $month = $eventFields[4];
        $year = 2020; // TODO: fix this later
        $this->start = DateTime::createFromFormat("Y-M-jS H:i", "$year-$month-$day $time");
    }

    protected function extractRunner(string $selection)
    {
        $sep = strpos($selection, ". ");
        $this->runner = substr($selection, $sep+2);
    }

    protected function store()
    {
        $db = Database::getInstance();

        $sql = "INSERT INTO $this->tableName (start, track, length, class, runner, stake, odds, profit) VALUES (?,?,?,?,?,?,?,?)";
        $statement = $db->prepare($sql);
        $statement->execute(
            [
                $this->start->format('Y-m-d H:i'),
                $this->track,
                $this->length,
                $this->class,
                $this->runner,
                $this->stake,
                $this->odds,
                $this->profit
            ]);
    }

}
