<?php

use App\Entity\Breakpoint;
use App\Entity\Enum\LoanDuration;
use App\Exception\HigherBreakpointNotFoundException;
use App\Exception\LowerBreakpointNotFoundException;
use App\Service\Interpolation\LinearStrategy;
use PHPUnit\Framework\TestCase;

class LinearInterpolationTest extends TestCase
{
    private array $breakpoints;

    protected function setUp(): void
    {
        $this->breakpoints = [
            new Breakpoint(LoanDuration::Term12, 1000, 50),
            new Breakpoint(LoanDuration::Term12, 2000, 90),
        ];
    }

    /**
     * @throws LowerBreakpointNotFoundException
     * @throws HigherBreakpointNotFoundException
     */
    public function testReturnsExactFee()
    {
        $linearStrategy = new LinearStrategy();
        $fee = $linearStrategy->calculate($this->breakpoints, 1000);

        $this->assertSame(50.00, $fee);

        $fee = $linearStrategy->calculate($this->breakpoints, 2000);

        $this->assertSame(90.00, $fee);
    }

    /**
     * @throws LowerBreakpointNotFoundException
     * @throws HigherBreakpointNotFoundException
     */
    public function testThrowsLowerBreakpointNotFound()
    {
        $breakpoints = [
            new Breakpoint(LoanDuration::Term12, 1100, 50),
            new Breakpoint(LoanDuration::Term12, 2000, 90),
        ];

        $this->expectException(LowerBreakpointNotFoundException::class);
        $this->expectExceptionMessage('Lower breakpoint not found in array');

        $linearStrategy = new LinearStrategy();
        $linearStrategy->calculate($breakpoints, 1000);
    }

    /**
     * @throws LowerBreakpointNotFoundException
     * @throws HigherBreakpointNotFoundException
     */
    public function testThrowsHigherBreakpointNotFound()
    {
        $breakpoints = [
            new Breakpoint(LoanDuration::Term12, 1000, 50),
            new Breakpoint(LoanDuration::Term12, 1400, 90),
        ];

        $this->expectException(HigherBreakpointNotFoundException::class);
        $this->expectExceptionMessage('Higher breakpoint not found in array');

        $linearStrategy = new LinearStrategy();
        $linearStrategy->calculate($breakpoints, 1500);
    }

    /**
     * @throws LowerBreakpointNotFoundException
     * @throws HigherBreakpointNotFoundException
     */
    public function testReturnsCorrectLinearInterpolationResult()
    {
        $linearStrategy = new LinearStrategy();
        $fee = $linearStrategy->calculate($this->breakpoints, 1500);

        $this->assertSame(70.00, $fee);

        $fee = $linearStrategy->calculate($this->breakpoints, 1800);

        $this->assertSame(82.00, $fee);

        $fee = $linearStrategy->calculate($this->breakpoints, 1800.25);

        $this->assertSame(82.01, $fee);
    }

    /**
     * @throws LowerBreakpointNotFoundException
     */
    public function testReturnsLowerBreakpoint()
    {
        $linearStrategy = new LinearStrategy();
        $lowerBreakpoint = $linearStrategy->getClosestLowerBreakpoint($this->breakpoints, 1500);

        $this->assertSame($this->breakpoints[0], $lowerBreakpoint);
    }

    /**
     * @throws HigherBreakpointNotFoundException
     */
    public function testReturnsHigherBreakpoint()
    {
        $linearStrategy = new LinearStrategy();
        $lowerBreakpoint = $linearStrategy->getClosestHigherBreakpoint($this->breakpoints, 1500);

        $this->assertSame($this->breakpoints[1], $lowerBreakpoint);
    }


}