<?php

require_once '../vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Dotenv\Dotenv;
use Calculator\Db\MortgageModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

try {
    $log = new Logger('report');
    $log->pushHandler(new StreamHandler('../log/request.log', Logger::INFO));
    $log->pushHandler(new StreamHandler('../log/error.log', Logger::ERROR, false));

    $dotenv = new Dotenv();
    $dotenv->load(dirname(__DIR__) . DIRECTORY_SEPARATOR . ".env");
    $data = MortgageModel::getTypes();
    $response = new JsonResponse();
    $request = Request::createFromGlobals();
    $log->info('incoming data types',[$request->query->all(),'path'=>$request->getBasePath()]);
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Methods', 'GET, POST');
    $response->headers->set('Access-Control-Allow-Headers', 'X-Requested-With');
    $response->headers->set('Content-Type', 'application/json');

    $response->setData(['data' => $data, 'errors' => ['error' => false, 'message' => '']]);
    $response->send();
} catch (Exception $e) {
    $log->error('error types', ['trace error' => $e->getTraceAsString()]);
    $response = new JsonResponse();
    $response->setData(['data' => [], 'errors' => ['error' => true, 'message' => $e->getMessage()]]);
} finally {
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Methods', 'GET, POST');
    $response->headers->set('Access-Control-Allow-Headers', 'X-Requested-With');
    if ($e) {
        $response->setStatusCode(400);
        $response->send();
    }else{
        $response->send();
    }
}
