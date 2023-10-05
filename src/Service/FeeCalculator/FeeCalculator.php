<?php

namespace App\Service\FeeCalculator;

use App\Entity\LoanProposal;
use App\Service\Breakpoint\Generator\CsvBreakpointGenerator;
use App\Service\Interpolation\InterpolationStrategyInterface;

class FeeCalculator implements FeeCalculatorInterface
{
    private InterpolationStrategyInterface $interpolationStrategy;

    public function __construct(
        private readonly CsvBreakpointGenerator $breakpointGenerator,
    ) {
    }

    public function calculate(LoanProposal $loanProposal): float
    {
        $loanAmount =  $loanProposal->getAmount();
        $breakpoints = $this->breakpointGenerator->getBreakpoints($loanProposal->getLoanDuration());
        $loanFee = $this->interpolationStrategy->calculate($breakpoints, $loanAmount);
        $totalLoanCost = $this->roundUpTo5($loanAmount + $loanFee);

        return $totalLoanCost - $loanAmount;
    }

    public function setInterpolationStrategy(InterpolationStrategyInterface $interpolationStrategy): void
    {
        $this->interpolationStrategy = $interpolationStrategy;
    }

    public function roundUpTo5(float $n): float
    {
        if (fmod($n, 5.0) == 0) {
            return $n;
        }

        return round(($n+5/2)/5)*5;
    }
}