<?php

namespace App\Service\Interpolation;

interface InterpolationStrategyInterface
{
    const NAME = '';
    public function calculate(array $breakpoints, float $loanAmount): float;
}