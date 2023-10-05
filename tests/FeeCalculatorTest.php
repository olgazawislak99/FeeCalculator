<?php

use App\Entity\Breakpoint;
use App\Entity\Enum\LoanDuration;
use App\Entity\LoanProposal;
use App\Exception\LoanNotDivisibleBy5Exception;
use App\Service\Breakpoint\Generator\CsvBreakpointGenerator;
use App\Service\FeeCalculator\FeeCalculator;
use App\Service\Interpolation\LinearStrategy;
use PHPUnit\Framework\TestCase;

class FeeCalculatorTest extends TestCase
{
    private FeeCalculator $feeCalculator;

    protected function setUp(): void
    {
        $breakpoints = [
            new Breakpoint(LoanDuration::Term12, 1000, 50),
            new Breakpoint(LoanDuration::Term12, 2000, 90),
        ];
        $csvBreakpointGenerator = $this->createMock(CsvBreakpointGenerator::class);
        $csvBreakpointGenerator
            ->method('getBreakpoints')
            ->willReturn($breakpoints);
        $this->feeCalculator = new FeeCalculator($csvBreakpointGenerator);
        $this->feeCalculator->setInterpolationStrategy(new LinearStrategy());
    }

    public function testTotalLoanCostDivisibleBy5()
    {
        for ($i = 0; $i < 5; $i++) {
            $amount = mt_rand(1000, 2000);
            $loanProposal = new LoanProposal(LoanDuration::Term12, $amount);
            $fee = $this->feeCalculator->calculate($loanProposal);

            $this->assertSame(0.0, fmod(($fee + $amount), 5));
        }
    }

    public function testReturnsExactFeeFromBreakpoint()
    {
        $loanProposal = new LoanProposal(LoanDuration::Term12, 1000);
        $fee = $this->feeCalculator->calculate($loanProposal);

        $this->assertSame(50.0, $fee);
    }

}