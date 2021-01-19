<?php

require_once '../vendor/autoload.php';

 use Symfony\Component\Dotenv\Dotenv;
 use Calculator\Db\Connection;
$dotenv = new Dotenv();
$dotenv->load(dirname(__DIR__).DIRECTORY_SEPARATOR.".env");

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

$connection=Connection::getInstance();
$query=$connection->query('SELECT * FROM `options`');
var_dump($connection->errorInfo());
$query->execute();
echo json_encode(
    [
        'firstPayment' => 1000000,
        'paymentPerMonth' => 20000,
        'priceObject'=>3000000,
        'priceTotal'=> 4500000,
        'percent'=>8,
        'error'=>false,
        'message'=>''
    ]
);