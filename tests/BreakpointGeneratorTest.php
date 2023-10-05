<?php

use App\Entity\Breakpoint;
use App\Entity\Enum\LoanDuration;
use App\Exception\BreakpointFileNotFoundException;
use App\Exception\WrongFileExtensionException;
use App\Service\Breakpoint\Generator\CsvBreakpointGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BreakpointGeneratorTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
       $this->validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
    }

    /**
     * @throws WrongFileExtensionException
     */
    public function testThrowsBreakpointFileNotFoundException()
    {
        $csvGenerator = new CsvBreakpointGenerator($this->validator,  'tests/Data/not_found.csv');

        $this->expectException(BreakpointFileNotFoundException::class);
        $this->expectExceptionMessage('File for breakpoint generation not found');

        $csvGenerator->generateBreakpoints(LoanDuration::Term12);
    }

    /**
     * @throws WrongFileExtensionException
     * @throws BreakpointFileNotFoundException
     */
    public function testThrowsWrongFileExtensionException()
    {
        $csvGenerator = new CsvBreakpointGenerator($this->validator,  'tests/Data/fee_test.pdf');

        $this->expectException(WrongFileExtensionException::class);
        $this->expectExceptionMessage('File extension pdf is wrong. Expected: csv');

        $csvGenerator->generateBreakpoints(LoanDuration::Term12);
    }

    /**
     * @throws WrongFileExtensionException
     * @throws BreakpointFileNotFoundException
     */
    public function testGenerateBreakpointsFromCsv()
    {
        $breakpoints = [
            new Breakpoint(LoanDuration::Term12, 1000, 50),
            new Breakpoint(LoanDuration::Term12, 2000, 90),
        ];
        $csvGenerator = new CsvBreakpointGenerator($this->validator,  'tests/Data/fee_test.csv');
        $csvGenerator->generateBreakpoints(LoanDuration::Term12);

        $this->assertEquals($breakpoints, $csvGenerator->getBreakpoints());
    }

}