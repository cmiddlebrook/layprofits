<?php

declare(strict_types = 1);
include 'autoloader.php';

echo "Profit Analyser\n\n";

echo "[1] Napchecker\n";
echo "[2] Horse Racing US\n";
echo "[3] Horse Racing AUS\n";
echo "[4] Greyhounds AUS\n";
echo "\nSelect strategy to analyse: ";

$input = trim(fgets(STDIN));
$strategyNumber = (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);

$profitAnalyser = new ProfitAnalyser();
$profitAnalyser->analyse($strategyNumber);
