<?php

use App\Entity\Enum\LoanDuration;
use App\Entity\LoanProposal;
use App\Exception\ValidationException\LoanProposalValidationException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class LoanProposalTest extends TestCase
{
    public function testLoanProposalConstructor()
    {
        $loanProposal = new LoanProposal(LoanDuration::Term12, 2600);

        $this->assertSame(LoanDuration::Term12, $loanProposal->getLoanDuration());
        $this->assertSame(2600.00, $loanProposal->getAmount());
    }

    public function testLoanAmountIsFloat()
    {
        $loanProposal = new LoanProposal(LoanDuration::Term12, 2600);

        $this->assertIsFloat($loanProposal->getAmount());
    }

    public function testValidatesAmountIsTooLow()
    {
        $loanProposal = new LoanProposal(LoanDuration::Term12, 999);

        $this->expectException(LoanProposalValidationException::class);
        $this->expectExceptionMessage(' This value should be between 1000 and 20000.');

        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($loanProposal);

        if (count($errors) > 0) {
            throw new LoanProposalValidationException((string) $errors);
        }
    }

    public function testValidatesAmountIsTooHigh()
    {
        $loanProposal = new LoanProposal(LoanDuration::Term12, 20001);

        $this->expectException(LoanProposalValidationException::class);
        $this->expectExceptionMessage(' This value should be between 1000 and 20000.');

        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($loanProposal);

        if (count($errors) > 0) {
            throw new LoanProposalValidationException((string) $errors);
        }
    }

    public function testValidatesMaxTwoDecimalPlaces()
    {
        $loanProposal = new LoanProposal(LoanDuration::Term12, 1000.2345);

        $this->expectException(LoanProposalValidationException::class);
        $this->expectExceptionMessage('The loan amount "1000.2345" have too much decimal places');

        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($loanProposal);

        if (count($errors) > 0) {
            throw new LoanProposalValidationException((string) $errors);
        }
    }
}