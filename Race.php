<?php

class GreyhoundRace
{
    private $country;
    private $track;
    private $start;
    private $length;
    private $grade;
    private $trap;
    private $runner;
    private $stake;
    private $odds;
    private $liability;
    private $profit;

    public function __construct($data)
    {
        $market_fields = explode("/",$data[0]);
        $this->country = trim($market_fields[0]);
        $meeting = trim($market_fields[1]);
        $meeting_fields = explode(" ", $meeting);
        $this->track = trim($meeting_fields[0]);
        $race = trim($market_fields[2]);
        $race_fields = explode(" ", $race);
        $time = trim($race_fields[0]);
        $this->length = trim($race_fields[2]);

        switch($this->country)
        {
            case "GB":
            {
                $daystr = trim($meeting_fields[1]);
                $month = trim($meeting_fields[2]);
                $this->grade = trim($race_fields[1]);
                break;
            }
            case "AU":
            {
                $daystr = trim($meeting_fields[2]);
                $month = trim($meeting_fields[3]);
                $this->grade = trim($race_fields[3]);
                break;
            }
            default:
            echo "Invalid country field: $country\n";
            break;
        }

        $day = (int) filter_var($daystr, FILTER_SANITIZE_NUMBER_INT);
        $year = 2020; // TODO: fix this later
        $this->start = new DateTime("$year-$month-$day $time");

        $selection = $data[1];
        $this->trap = $selection[0];
        $this->runner = substr($selection, 3);

        $this->stake = floatval($data[2]);
        $this->liability = floatval($data[3]);
        $this->odds = floatval($data[4]);

        $bfprofit = $data[5];
        $this->profit = floatval($bfprofit);
        // Betfair formats negative amounts as (1.23), so need to format differently in that case
        $len = strlen($bfprofit);
        if (strpos($bfprofit, ")") > 0)
        {
            $loss_string = substr($bfprofit, 1, $len-1);
            $this->profit = 0 - floatval($loss_string);
        }
    }

    public function print()
    {
        $start_string = $this->start->format("d/m/y H:i");
        echo "$this->country, $this->track, $start_string, $this->grade, $this->length, $this->trap, $this->runner, Odds $this->odds, Stake $this->stake, Lia $this->liability,  profit $this->profit\n";
    }
}
