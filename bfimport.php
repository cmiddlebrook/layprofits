<?php
include 'Race.php';

echo "Betfair Importer\n";

$bfcsv = fopen("bfgh.csv", "r");
if ($bfcsv == FALSE)
{
    echo "There was an error opening the csv file\n";
    exit;
}

$races = array();
while(($data = fgetcsv($bfcsv, 1000, ",")) !== FALSE)
{
    $race = new GreyhoundRace($data);
    array_push($races, $race);
}
echo count($races) . " races parsed\n";

fclose($bfcsv);
