<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\LoanDuration;
use App\Validator\MaxTwoDecimalPlaces;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A cut down version of a loan application containing
 * only the required properties for this test.
 */
class LoanProposal
{
    const MIN_LOAN_AMOUNT = 1000;
    const MAX_LOAN_AMOUNT = 20000;

    #[Assert\NotBlank]
    private LoanDuration $loanDuration;

    #[Assert\Range(
        min: self::MIN_LOAN_AMOUNT,
        max: self::MAX_LOAN_AMOUNT,
    )]
    #[MaxTwoDecimalPlaces]
    #[Assert\NotBlank]
    private float $amount;

    public function __construct(LoanDuration $loanDuration, float $amount)
    {
        $this->loanDuration = $loanDuration;
        $this->amount = $amount;
    }

    public function getLoanDuration(): LoanDuration
    {
        return $this->loanDuration;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
