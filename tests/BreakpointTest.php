<?php

use App\Entity\Breakpoint;
use App\Entity\Enum\LoanDuration;
use App\Exception\ValidationException\BreakpointValidationException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class BreakpointTest extends TestCase
{
    public function testLoanProposalConstructor()
    {
        $breakpoint = new Breakpoint(LoanDuration::Term12, 2600, 100);

        $this->assertSame(LoanDuration::Term12, $breakpoint->getLoanDuration());
        $this->assertSame(2600.00, $breakpoint->getAmount());
        $this->assertSame(100.00, $breakpoint->getFee());
    }

    public function testAmountIsFloat()
    {
        $breakpoint = new Breakpoint(LoanDuration::Term12, 2600, 100);

        $this->assertIsFloat($breakpoint->getAmount());
    }

    public function testFeeIsFloat()
    {
        $breakpoint = new Breakpoint(LoanDuration::Term12, 2600, 100);

        $this->assertIsFloat($breakpoint->getAmount());
    }

    public function testValidatesFeeGreaterThanZero()
    {
        $breakpoint = new Breakpoint(LoanDuration::Term12, 2600, -1);

        $this->expectException(BreakpointValidationException::class);
        $this->expectExceptionMessage('This value should be greater than or equal to 0.');

        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($breakpoint);

        if (count($errors) > 0) {
            throw new BreakpointValidationException((string) $errors);
        }
    }

    public function testValidatesAmountIsTooLow()
    {
        $breakpoint = new Breakpoint(LoanDuration::Term12, 2, 70);

        $this->expectException(BreakpointValidationException::class);
        $this->expectExceptionMessage(' This value should be between 1000 and 20000.');

        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($breakpoint);

        if (count($errors) > 0) {
            throw new BreakpointValidationException((string) $errors);
        }
    }

    public function testValidatesAmountIsTooHigh()
    {
        $breakpoint = new Breakpoint(LoanDuration::Term12, 2, 30000);

        $this->expectException(BreakpointValidationException::class);
        $this->expectExceptionMessage(' This value should be between 1000 and 20000.');

        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($breakpoint);

        if (count($errors) > 0) {
            throw new BreakpointValidationException((string) $errors);
        }
    }

}