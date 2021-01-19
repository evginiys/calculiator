<?php

namespace Calculator\Calculate;

use Exception;
use Generator;

class Calculator
{
    private ?float $yearPercent;
    private ?int $price;
    private ?int $term;
    private ?int $firstPayment;
    private ?float $totalRate;
    private ?int $bodyCredit;
    private ?int $totalPay;

    private ?float $everyMonthInterestRate;
    private ?int $everyMonthPayment;
    private ?int $overpayment;
    private ?int $balanceOwed;

    /**
     * Calculator constructor.
     *
     * @param float $yearPercent
     * @param int   $price
     * @param int   $term
     * @param int   $firstPayment
     *
     * @throws Exception
     */
    public function __construct(float $yearPercent, int $price, int $term, int $firstPayment)
    {
        if ($yearPercent < 0 or $price < 0 or $term < 0 or $firstPayment < 0) {
            throw new Exception(
                "Values might be higher then 0 yearPercent=$yearPercent , 
            price = $price , term = $term , firstPayment = $firstPayment"
            );
        }
        if ($price - $firstPayment < 0) {
            throw new Exception('first payment bigger then price');
        }
        $this->firstPayment = $firstPayment;
        $this->price = $price;
        $this->term = $term;
        $this->yearPercent = $yearPercent;
        $this->bodyCredit = $this->price - $this->firstPayment;
        $this->balanceOwed = $this->bodyCredit;
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
        return $this->overpayment = $this->totalPay - $this->price;
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

}