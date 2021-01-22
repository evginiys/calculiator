<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Calculator\Calculate\Calculator;
use Calculator\Calculate\CheckCalculatorData;
use Calculator\Calculate\Exception\InvalidDataFromDbException;
use Calculator\Db\MortgageModel;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


try {
    $log = new Logger('report');
    $log->pushHandler(new StreamHandler('../log/request.log', Logger::DEBUG));
    $log->pushHandler(new StreamHandler('../log/error.log', Logger::ERROR, false));

    $dotenv = new Dotenv();
    $dotenv->load(dirname(__DIR__) . DIRECTORY_SEPARATOR . ".env");

    $request = Request::createFromGlobals();

    $log->info('income request', [$request->query->all(),'path'=>$request->getBasePath()]);

    if (!$request->query->has('term')
        or !$request->query->has('price')
        or !$request->query->has('firstPayment')
        or !$request->query->has('percent')
        or !$request->query->has('typeId')) {
        throw new InvalidArgumentException('Incorrect incoming data');
    }
    $term = $request->query->get('term');
    $price = $request->query->get('price');
    $firstPayment = $request->query->get('firstPayment');
    $percent = $request->query->get('percent');
    $typeId = $request->query->get('typeId');
    if ($request->query->has('interType')) {
        $interType = $request->query->get('interType');
    } else {
        $interType = MortgageModel::TYPE_USUAL;
    }

    if (!is_numeric($term) or !is_numeric($price) or !is_numeric($firstPayment) or !is_numeric($percent)) {
        throw new InvalidArgumentException('Incorrect incoming data: values must be Number');
    }
    $calculator = new Calculator((float)$percent, (int)$price, (int)$term, (float)$firstPayment);
    $typeId = filter_var($typeId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$typeId) {
        throw new InvalidArgumentException('Incorrect incoming data: values must be Number');
    }
    $interType = filter_var($interType, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$interType) {
        $interType = MortgageModel::TYPE_USUAL;
    }
    $template = MortgageModel::getOption($typeId, $interType);
    $checkData = new CheckCalculatorData($calculator, $template);
    $checkData->checkData();

    $response = new JsonResponse();
    $calculator->calculate();
    $everyMonthPayment = $calculator->calculatePartsOfEveryMonthPayment();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('B2', 'Стоимость объекта ');
    $sheet->setCellValue('B3', $calculator->getPrice());
    $sheet->setCellValue('C2', 'Первичный взнос ');
    $sheet->setCellValue('C3', $calculator->getFirstPayment() * $calculator->getPrice() / 100);
    $sheet->setCellValue('D2', 'Сумма кредита ');
    $sheet->setCellValue('D3', $calculator->getBodyCredit());
    $sheet->setCellValue('E2', 'Процентная ставка ');
    $sheet->setCellValue('E3', $calculator->getYearPercent());
    $sheet->setCellValue('F2', "Срок кредитования ");
    $sheet->setCellValue('F3', $calculator->getTerm());
    $sheet->setCellValue('H2', 'Общая сумма выплат ');
    $sheet->setCellValue('H3', $calculator->calculateTotalPay());
    $sheet->setCellValue('G2', "Сумма переплаты ");
    $sheet->setCellValue('G3', $calculator->getOverpayment());
    $sheet->setCellValue('I2', "Ежемесячный платеж ");
    $sheet->setCellValue('I3', $calculator->getEveryMonthPayment());

    $sheet->setCellValue('B5', "Общая сумма платежа ");
    $sheet->setCellValue('C5', "Часть на основной долг ");
    $sheet->setCellValue('D5', "Часть на проценты ");
    $sheet->setCellValue('E5', "Остаток долга ");
    $iterator = 6;
    foreach ($everyMonthPayment as $value) {
        $sheet->setCellValue('B' . $iterator, $calculator->getEveryMonthPayment() . PHP_EOL);
        $sheet->setCellValue('C' . $iterator, round($value['mainPart'], 2) . PHP_EOL);
        $sheet->setCellValue('D' . $iterator, round($value['procentPart'], 2) . PHP_EOL);
        if ($value['balanceOwed'] < 0) {
            $sheet->setCellValue('E' . $iterator, 0);
        } else {
            $sheet->setCellValue('E' . $iterator, $value['balanceOwed']);
        }
        $iterator++;
    }
    if ($request->query->has('xlsx') and $request->query->get('xlsx')) {
        $writer = new Xlsx($spreadsheet);
        $fileName = md5((string)time()) . '.xlsx';
        $ext = '.xlsx';
        $writer->save($fileName);
    } else {
        $writer = new Mpdf($spreadsheet);
        $fileName = md5((string)time()) . '.pdf';
        $ext = '.pdf';
        $writer->save($fileName);
    }


    $fileResponse = new BinaryFileResponse($fileName);

    $fileResponse->headers->set('Content-Type', 'text/plain');
    $fileResponse->setContentDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        'report' . $ext
    );
    $fileResponse->deleteFileAfterSend();
    $fileResponse->send();
} catch (InvalidDataFromDbException $e) {
    $log->error('Db error', ['trace error' => $e->getTraceAsString()]);
    $response = new JsonResponse();
    $result = ['data' => [], 'errors' => ['error' => true, 'message' => $e->getMessage()]];
    $response->setData($result);
} catch (InvalidArgumentException $e) {
    $log->error('Incorrect incoming data', ['trace error' => $e->getTraceAsString()]);
    $response = new JsonResponse();
    $result = ['data' => [], 'errors' => ['error' => true, 'message' => $e->getMessage()]];
    $response->setData($result);
} catch (Exception $e) {
    $response = new JsonResponse();
    $result = ['data' => [], 'errors' => ['error' => true, 'message' => $e->getMessage()]];
    $response->setData($result);
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
