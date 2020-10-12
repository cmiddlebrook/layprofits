<?php
declare(strict_types = 1);
include 'autoloader.php';

echo "BF Bot Manager Importer\n\n";

$directoryPath = "E:\DropBox\Work\Sports Betting\Value Betting\BF Bot Manager";
$files = scandir($directoryPath);
unset($files[0]);
unset($files[1]);

echo "[0] - Analyse existing data\n";
foreach($files as $index => $file)
{
     echo "[$index] - $file\n";
}

echo "\nChoose file to import: ";

$input = trim(fgets(STDIN));
$fileNr = (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);

$bsf = new BettingStrategyFactory();

if (0 == $fileNr)
{
    echo "[1] Napchecker\n";
    echo "[2] Horse Racing US\n";
    echo "[3] Horse Racing AUS\n";
    echo "[4] Greyhounds AUS\n";
    echo "\nSelect strategy: ";

    $input = trim(fgets(STDIN));
    $strategyNr = (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    $bsf->analyse($strategyNr);
    exit();
}

$fileName = $directoryPath . "\\" . $files[$fileNr];

$csvFile = fopen("$fileName", "r");
if ($fileName == FALSE)
{
    echo "There was an error opening the csv file\n";
    exit;
}

$count = 0;
while(($data = fgetcsv($csvFile, 1000, ",")) !== FALSE)
{
    $bsf->parseBet($data);
    $count++;
}

echo "$count races parsed\n";
fclose($csvFile);
