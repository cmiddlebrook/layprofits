<?php
include 'Race.php';

echo "Betfair Importer\n";

$bfcsv = fopen("bfgh.csv", "r");
if ($bfcsv == FALSE)
{
    echo "There was an error opening the csv file\n";
    exit;
}

$row = 1;
while(($data = fgetcsv($bfcsv, 1000, ",")) !== FALSE)
{
    $race = new Race($data);
    $row++;
}
fclose($bfcsv);
