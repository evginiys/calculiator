<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Calculator\Calculate\Calculator;
use Calculator\Db\Connection;
use Symfony\Component\Dotenv\Dotenv;


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

$dotenv = new Dotenv();
$dotenv->load(dirname(__DIR__) . DIRECTORY_SEPARATOR . ".env");

$calculator = new Calculator(5, 2_040_000, 300, 360_000);

echo $calculator->calculateEveryMonthInterestRate().PHP_EOL;
echo $calculator->calculateTotalRate().PHP_EOL;
echo $calculator->calculateEveryMonthPayment().PHP_EOL;
echo $calculator->calculateTotalPay().PHP_EOL;
echo $calculator->calculateOverPayment().PHP_EOL;
foreach ($calculator->calculatePartsOfEveryMonthPayment() as $value){
    print_r($value);
}
