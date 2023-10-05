<?php

namespace App\Service\Interpolation;

use App\Entity\Breakpoint;
use App\Exception\HigherBreakpointNotFoundException;
use App\Exception\LowerBreakpointNotFoundException;

class LinearStrategy implements InterpolationStrategyInterface
{
    const NAME = 'Linear';

    /**
     * @throws HigherBreakpointNotFoundException
     * @throws LowerBreakpointNotFoundException
     */
    public function calculate(array $breakpoints, float $loanAmount): float
    {
        /** @var Breakpoint $breakpoint */
        foreach ($breakpoints as $breakpoint) {
            if ($breakpoint->getAmount() === $loanAmount) {
                return $breakpoint->getFee();
            }
        }

        $lowerBreakpoint = $this->getClosestLowerBreakpoint($breakpoints, $loanAmount);
        $higherBreakpoint = $this->getClosestHigherBreakpoint($breakpoints, $loanAmount);
        $lowerAmount = $lowerBreakpoint->getAmount();
        $higherAmount = $higherBreakpoint->getAmount();
        $lowerFee = $lowerBreakpoint->getFee();
        $higherFee = $higherBreakpoint->getFee();
        /*
         * Linear interpolation
         * d = (loanAmount - lowerAmount) / (higherAmount - lowerAmount)
         * loanFee = lowerFee * (1 - d) + higherFee * d
         */

        $d = ($loanAmount - $lowerAmount)/($higherAmount - $lowerAmount);

        return $lowerFee * (1 - $d) + $higherFee * $d;
    }

    /**
     * @throws LowerBreakpointNotFoundException
     */
    function getClosestLowerBreakpoint(array $breakpoints, float $loanAmount): Breakpoint
    {
        $lowerBreakpoint = null;
        /** @var Breakpoint $breakpoint */
        foreach ($breakpoints as $breakpoint)
        {
            $breakpointAmount = $breakpoint->getAmount();

            if (($breakpointAmount < $loanAmount) &&
                (is_null($lowerBreakpoint) or ($lowerBreakpoint->getAmount() < $breakpointAmount)))
            {
                $lowerBreakpoint = $breakpoint;
            }
        }

        if ($lowerBreakpoint === null) {
            throw new LowerBreakpointNotFoundException;
        }

        return $lowerBreakpoint;
    }

    /**
     * @throws HigherBreakpointNotFoundException
     */
    function getClosestHigherBreakpoint(array $breakpoints, float $loanAmount): Breakpoint
    {
        $higherBreakpoint = null;
        /** @var Breakpoint $breakpoint */
        foreach ($breakpoints as $breakpoint)
        {
            $breakpointAmount = $breakpoint->getAmount();

            if (($breakpointAmount > $loanAmount) &&
                (is_null($higherBreakpoint) or ($higherBreakpoint->getAmount() > $breakpointAmount)))
            {
                $higherBreakpoint = $breakpoint;
            }
        }

        if ($higherBreakpoint === null) {
            throw new HigherBreakpointNotFoundException();
        }

        return $higherBreakpoint;
    }
}