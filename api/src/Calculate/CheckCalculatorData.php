<?php


namespace Calculator\Calculate;

use Calculator\Calculate\Exception\InvalidDataException;
use Calculator\Calculate\Exception\InvalidDataFromDbException;
use Calculator\Db\MortgageModel;
use InvalidArgumentException;


use function abs;


class CheckCalculatorData
{
    private Calculator $calculator;
    private array $templateData;

    public function __construct(Calculator $calculator, array $templateData)
    {
        $this->calculator = $calculator;
        $this->templateData = $templateData;
    }


    public function checkData(): bool
    {
        if (empty($this->templateData[0])) {
            throw new InvalidDataFromDbException(
                "Not found record in table option "
            );
        }
        if (!key_exists('firstpaymentmin', $this->templateData[0]) or
            !key_exists('firstpaymentmax', $this->templateData[0]) or
            !key_exists('pricemin', $this->templateData[0]) or
            !key_exists('pricemax', $this->templateData[0]) or
            !key_exists('termmin', $this->templateData[0]) or
            !key_exists('termmax', $this->templateData[0]) or
            !key_exists('percent', $this->templateData[0])) {
            throw new InvalidDataFromDbException(
                "incorect template data"
            );
        }
        if (!$this->checkFirstPayment(
            $this->calculator->getFirstPayment(),
            $this->templateData[0]['firstpaymentmin'],
            $this->templateData[0]['firstpaymentmax']
        )) {
            throw new InvalidDataException('First payment is incorrect');
        }
        if (!$this->checkPercent(
            $this->calculator->getYearPercent(),
            $this->templateData[0]['percent']
        )) {
            throw new InvalidDataException('Percent is incorrect');
        }

        if (!$this->checkTerm(
            $this->calculator->getTerm(),
            $this->templateData[0]['termmin'],
            $this->templateData[0]['termmax']
        )) {
            throw new InvalidDataException('term is incorrect');
        }

        if (!$this->checkPrice(
            $this->calculator->getPrice(),
            $this->templateData[0]['pricemin'],
            $this->templateData[0]['pricemax']
        )) {
            throw new InvalidDataException('price is incorrect');
        }
        return true;
    }

    /**
     * @param float $firstPayment
     * @param float $firstPaymentMin
     * @param float $firstPaymentMax
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function checkFirstPayment(float $firstPayment, float $firstPaymentMin, float $firstPaymentMax): bool
    {
        if ($firstPayment < 0 or $firstPaymentMin < 0 or $firstPaymentMax < 0) {
            throw new InvalidArgumentException('firstPayment firstPaymentMin and firstPaymentMax must be higher 0');
        }
        if ($firstPaymentMax > 100) {
            throw new InvalidArgumentException('firstPaymentMax must be less then 100(%)');
        }
        if ($firstPaymentMin <= $firstPayment and $firstPayment <= $firstPaymentMax) {
            return true;
        }
        return false;
    }

    /**
     * @param float $percent
     * @param float $percentTemplate
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function checkPercent(float $percent, float $percentTemplate): bool
    {
        if ($percent < 0 or $percentTemplate < 0) {
            throw new InvalidArgumentException('percent and percentTemplate must be higher 0');
        }
        return abs($percent - $percentTemplate) < PHP_FLOAT_EPSILON;
    }

    /**
     * @param float $term
     * @param float $termMin
     * @param float $termMax
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function checkTerm(float $term, float $termMin, float $termMax): bool
    {
        if ($term < 0 or $termMin < 0 or $termMax < 0) {
            throw new InvalidArgumentException('term termMin and termMax must be higher 0');
        }
        if ($termMin <= $term and $term <= $termMax) {
            return true;
        }
        return false;
    }

    /**
     * @param float $price
     * @param float $priceMin
     * @param float $priceMax
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function checkPrice(float $price, float $priceMin, float $priceMax): bool
    {
        if ($price < 0 or $priceMin < 0 or $priceMax < 0) {
            throw new InvalidArgumentException('firstPayment firstPaymentMin and firstPaymentMax must be higher 0');
        }
        if ($priceMin <= $price and $price <= $priceMax) {
            return true;
        }
        return false;
    }

}