<?php
declare(strict_types = 1);
include 'autoloader.php';

echo "BF Bot Manager Importer\n\n";

$directoryPath = "E:\DropBox\Work\Sports Betting\Value Betting\BF Bot Manager";
$files = scandir($directoryPath);
unset($files[0]);
unset($files[1]);

foreach($files as $index => $file)
{
     echo "[$index] - $file\n";
}

echo "\nChoose file to import: ";

$input = trim(fgets(STDIN));
$fileNr = (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);

$fileName = $directoryPath . "\\" . $files[$fileNr];

$csvFile = fopen("$fileName", "r");
if ($fileName == FALSE)
{
    echo "There was an error opening the csv file\n";
    exit;
}

$db = Database::getInstance();

$bsf = new BettingStrategyFactory();
while(($data = fgetcsv($csvFile, 1000, ",")) !== FALSE)
{
    $bsf->parseBet($data);
}

fclose($csvFile);
