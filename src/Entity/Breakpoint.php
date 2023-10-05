<?php

namespace App\Entity;

use App\Entity\Enum\LoanDuration;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\VarDumper\Cloner\Data;

class Breakpoint
{
    #[Assert\NotBlank]
    private LoanDuration $loanDuration;

    #[Assert\NotBlank]
    #[Assert\Range(
        min: LoanProposal::MIN_LOAN_AMOUNT,
        max: LoanProposal::MAX_LOAN_AMOUNT,
    )]
    private float $amount;
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(0)]
    private float $fee;

    /**
     * @param LoanDuration $loanDuration
     * @param float $amount
     * @param float $fee
     */
    public function __construct(LoanDuration $loanDuration, float $amount, float $fee)
    {
        $this->loanDuration = $loanDuration;
        $this->amount = $amount;
        $this->fee = $fee;
    }

    public function getLoanDuration(): LoanDuration
    {
        return $this->loanDuration;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getFee(): float
    {
        return $this->fee;
    }
}