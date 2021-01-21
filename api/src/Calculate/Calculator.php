<?php

namespace Calculator\Calculate;

use Exception;
use Generator;
use http\Exception\InvalidArgumentException;

class Calculator
{
    private float $yearPercent;
    private int $price;
    private int $term;
    private float $firstPayment;
    private float $totalRate;
    private int $bodyCredit;
    private int $totalPay;

    private float $everyMonthInterestRate;
    private int $everyMonthPayment;
    private int $overpayment;
    private int $balanceOwed;

    /**
     * Calculator constructor.
     *
     * @param float $yearPercent
     * @param int   $price
     * @param int   $term
     * @param float $firstPayment
     *
     * @throws InvalidArgumentException
     */
    public function __construct(float $yearPercent, int $price, int $term, float $firstPayment)
    {
        if ($yearPercent < 0 or $price < 0 or $term < 0 or $firstPayment < 0) {
            throw new InvalidArgumentException(
                "Values might be higher then 0 yearPercent=$yearPercent , 
            price = $price , term = $term , firstPayment = $firstPayment"
            );
        }
        if ($price - $firstPayment * $price / 100 < 0) {
            throw new InvalidArgumentException('first payment bigger then price');
        }
        $this->firstPayment = $firstPayment;
        $this->price = $price;
        $this->term = $term;
        $this->yearPercent = $yearPercent;
        $this->bodyCredit = $this->price - $this->firstPayment * $this->price / 100;
        $this->balanceOwed = $this->bodyCredit;
    }

    /**
     * @return array
     */
    public function calculate(): array
    {
        $result['everyMonthRate'] = $this->calculateEveryMonthInterestRate();
        $result['totalRate'] = $this->calculateTotalRate();
        $result['everyMonthPayment'] = $this->calculateEveryMonthPayment();
        $result['totalPay'] = $this->calculateTotalPay();
        $result['overPayment'] = $this->calculateOverPayment();
        return $result;
    }

    /**
     * @return float
     */
    public function calculateEveryMonthInterestRate(): float
    {
        return $this->everyMonthInterestRate = $this->yearPercent / 12 / 100;
    }

    /**
     * @return float
     */
    public function calculateTotalRate(): float
    {
        return $this->totalRate = pow((1 + $this->everyMonthInterestRate), $this->term);
    }

    /**
     * @return int
     */
    public function calculateEveryMonthPayment(): int
    {
        return $this->everyMonthPayment = $this->bodyCredit * $this->everyMonthInterestRate * $this->totalRate / ($this->totalRate - 1);
    }

    /**
     * @return int
     */
    public function calculateTotalPay(): int
    {
        return $this->totalPay = $this->everyMonthPayment * $this->term;
    }

    /**
     * @return int
     */
    public function calculateOverPayment(): int
    {
        return $this->overpayment = $this->totalPay - $this->bodyCredit;
    }

    /**
     * @return Generator|null
     */
    public function calculatePartsOfEveryMonthPayment(): ?Generator
    {
        for ($i = $this->term; $i > 0; $i--) {
            if ($this->balanceOwed < 0) {
                return null;
            }
            $procentPart = $this->balanceOwed * $this->everyMonthInterestRate;
            $mainPart = $this->everyMonthPayment - $procentPart;
            $this->balanceOwed -= $mainPart;
            yield ['procentPart' => $procentPart, 'mainPart' => $mainPart, 'balanceOwed' => $this->balanceOwed];
        }
    }

    /**
     * @return int
     */
    public function getEveryMonthPayment(): int
    {
        return $this->everyMonthPayment;
    }

    /**
     * @return float|null
     */
    public function getFirstPayment(): float
    {
        return $this->firstPayment;
    }

    /**
     * @return int|null
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @return int|null
     */
    public function getTerm(): int
    {
        return $this->term;
    }

    /**
     * @return float|null
     */
    public function getYearPercent(): float
    {
        return $this->yearPercent;
    }

    /**
     * @return float|int
     */
    public function getBodyCredit()
    {
        return $this->bodyCredit;
    }

    /**
     * @return int
     */
    public function getOverpayment(): int
    {
        return $this->overpayment;
    }
}