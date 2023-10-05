<?php

declare(strict_types=1);

namespace App\Service\FeeCalculator;

use App\Entity\LoanProposal;
use App\Service\Interpolation\InterpolationStrategyInterface;
use App\Service\Interpolation\LinearStrategy;

interface FeeCalculatorInterface
{
    /**
     * @return float The calculated total fee.
     */
    public function calculate(LoanProposal $loanProposal): float;

    public function setInterpolationStrategy(InterpolationStrategyInterface $interpolationStrategy): void;
}
