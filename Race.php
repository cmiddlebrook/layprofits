<?php

class Race
{
    public function __construct($data)
    {
        $market_fields = explode("/",$data[0]);
        $country = trim($market_fields[0]);
        $meeting = trim($market_fields[1]);
        $meeting_fields = explode(" ", $meeting);
        $track = trim($meeting_fields[0]);
        $race = trim($market_fields[2]);
        $race_fields = explode(" ", $race);
        $time = trim($race_fields[0]);
        $length = trim($race_fields[2]);

        switch($country)
        {
            case "GB":
            {
                $daystr = trim($meeting_fields[1]);
                $month = trim($meeting_fields[2]);
                $grade = trim($race_fields[1]);
                break;
            }
            case "AU":
            {
                $daystr = trim($meeting_fields[2]);
                $month = trim($meeting_fields[3]);
                $grade = trim($race_fields[3]);
                break;
            }
            default:
            echo "Invalid country field: $country\n";
            break;
        }

        $day = (int) filter_var($daystr, FILTER_SANITIZE_NUMBER_INT);
        $year = 2020; // TODO: fix this later
        $race_start = new DateTime("$year-$month-$day $time");
        $start_string = $race_start->format("d/m/y H:i");

        $selection = $data[1];
        $trap = $selection[0];
        $runner = substr($selection, 3);

        $stake = floatval($data[2]);
        $liability = floatval($data[3]);
        $odds = floatval($data[4]);

        $bfprofit = $data[5];
        $profit = floatval($bfprofit);
        // Betfair formats negative amounts as (1.23), so need to format differently in that case
        $len = strlen($bfprofit);
        if (strpos($bfprofit, ")") > 0)
        {
            $loss_string = substr($bfprofit, 1, $len-1);
            $profit = 0 - floatval($loss_string);
        }


        echo "$country, $track, $start_string, $grade, $length, $trap, $runner, Odds $odds, Stake $stake, Lia $liability,  profit $profit\n";

    }
}
