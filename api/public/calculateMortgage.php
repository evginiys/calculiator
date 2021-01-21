<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Calculator\Calculate\Calculator;
use Calculator\Calculate\CheckCalculatorData;
use Calculator\Calculate\Exception\InvalidDataFromDbException;
use Calculator\Db\MortgageModel;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


try {
    $log = new Logger('report');
    $log->pushHandler(new StreamHandler('../log/request.log', Logger::INFO));
    $log->pushHandler(new StreamHandler('../log/error.log', Logger::ERROR, false));

    $dotenv = new Dotenv();
    $dotenv->load(dirname(__DIR__) . DIRECTORY_SEPARATOR . ".env");
    $request = Request::createFromGlobals();
    $log->info('incoming data calculateMortgage',[$request->query->all(),'path'=>$request->getBasePath()]);
    if (!$request->query->has('term')
        or !$request->query->has('price')
        or !$request->query->has('firstPayment')
        or !$request->query->has('percent')
        or !$request->query->has('typeId')) {
        throw new InvalidArgumentException('Incorrect incoming data');
    }
    $term=$request->query->get('term');
    $price=$request->query->get('price');
    $firstPayment=$request->query->get('firstPayment');
    $percent=$request->query->get('percent');
    $typeId=$request->query->get('typeId');
    if ($request->query->has('interType')){
        $interType=$request->query->get('interType');
    }else{
        $interType=MortgageModel::TYPE_USUAL;
    }

    if (!is_numeric($term) or !is_numeric($price) or !is_numeric($firstPayment) or !is_numeric($percent)){
        throw new InvalidArgumentException('Incorrect incoming data: values must be Number');
    }
    $calculator = new Calculator((float)$percent, (int)$price, (int)$term, (float)$firstPayment);
    $typeId=filter_var($typeId,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
    if (!$typeId){
        throw new InvalidArgumentException('Incorrect incoming data: values must be Number');
    }
    $interType=filter_var($interType,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
    if (!$interType){
        $interType=MortgageModel::TYPE_USUAL;
     }
    $template = MortgageModel::getOption($typeId, $interType);
    $checkData = new CheckCalculatorData($calculator, $template);
    $checkData->checkData();
    $response = new JsonResponse();

    $data=$calculator->calculate();
    $result=['data'=>$data,'errors'=>['error'=>false,'message'=>'']];
    $response->setData($result);

} catch (InvalidDataFromDbException $e) {
    $log->error('Db error calculateMortgage', ['trace error' => $e->getTraceAsString()]);
    $response = new JsonResponse();
    $result = ['data' => [], 'errors' => ['error' => true, 'message' => $e->getMessage()]];
    $response->setData($result);
} catch (InvalidArgumentException $e) {
    $log->error('Incorrect incoming data calculateMortgage', ['trace error' => $e->getTraceAsString()]);
    $response = new JsonResponse();
    $result = ['data' => [], 'errors' => ['error' => true, 'message' => $e->getMessage()]];
    $response->setData($result);
}catch (Exception $e) {
    $log->error('Error calculateMortgage', ['trace error' => $e->getTraceAsString()]);
    $response = new JsonResponse();
    $result=['data'=>[],'errors'=>['error'=>true,'message'=>$e->getMessage()]];
    $response->setData($result);
} finally {
    if ($e) {
        $response->setStatusCode(400);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST');
        $response->headers->set('Access-Control-Allow-Headers', 'X-Requested-With');
        $response->send();
    }
}
