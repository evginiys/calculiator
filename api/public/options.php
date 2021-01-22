<?php

require_once '../vendor/autoload.php';

use Calculator\Calculate\Exception\InvalidDataFromDbException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Dotenv\Dotenv;
use Calculator\Db\MortgageModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

try {
    $log = new Logger('report');
    $log->pushHandler(new StreamHandler('../log/request.log', Logger::INFO));
    $log->pushHandler(new StreamHandler('../log/error.log', Logger::ERROR, false));

    $dotenv = new Dotenv();
    $dotenv->load(dirname(__DIR__) . DIRECTORY_SEPARATOR . ".env");
    $request = Request::createFromGlobals();
    $log->info('incoming data option',[$request->query->all(),'path'=>$request->getBasePath()]);
    if (!$request->query->has('typeId')) {
        throw new InvalidArgumentException('Not found typeId argument');
    }
    $typeId = $request->query->get('typeId');
    if (is_numeric($typeId)) {
        $typeId = 0 + $typeId;
        if (!is_int($typeId) or $typeId <= 0) {
            throw new InvalidArgumentException('TypeId is incorrect');
        }
    } else {
        throw new InvalidArgumentException('TypeId is incorrect');
    }

    if ($request->query->has('intertype')) {
        $interType=$request->query->get('intertype');
    }else {
        $interType = MortgageModel::TYPE_USUAL;
    }

    $data = MortgageModel::getOption($typeId,$interType);
    if (!key_exists(0, $data)  or empty($data[0])) {
        throw new InvalidDataFromDbException('Not found option');
    }
    $response = new JsonResponse();

    $response->setData(['data' => $data, 'errors' => ['error' => false, 'message' => '']]);

} catch (Exception $e) {
    $log->error('Error option', [' error' => $e->getMessage()]);
    $response = new JsonResponse();
    $response->setData(['data' => [], 'errors' => ['error' => true, 'message' => $e->getMessage()]]);
    $response->setStatusCode(400);
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