<?php

namespace App\Service\Breakpoint\Generator;

use App\Entity\Breakpoint;
use App\Entity\Enum\LoanDuration;
use App\Exception\BreakpointFileNotFoundException;
use App\Exception\ValidationException\BreakpointValidationException;
use App\Exception\WrongFileExtensionException;

class CsvBreakpointGenerator extends AbstractBreakpointGenerator
{
    const EXTENSION = 'csv';

    /**
     * @throws BreakpointValidationException
     * @throws BreakpointFileNotFoundException
     * @throws WrongFileExtensionException
     */
    public function generateBreakpoints(LoanDuration $loanDuration): void
    {
        $file = $this->getFile();
        $this->checkFileExtension(self::EXTENSION);
        $rows = array_map('str_getcsv', $file);
        $headers = array_shift($rows);
        $rawBreakpoints = [];
        foreach ($rows as $row) {
            $rawBreakpoints[] = array_combine($headers, $row);
        }

        foreach ($rawBreakpoints as $rawBreakpoint)
        {
            if ((int)$rawBreakpoint['term'] === $loanDuration->value) {
                $breakpoint = new Breakpoint($loanDuration, $rawBreakpoint['amount'], $rawBreakpoint['fee']);
                $errors = $this->validator->validate($breakpoint);

                if (count($errors) > 0) {
                    throw new BreakpointValidationException();
                }

                $breakpoints[] = $breakpoint;
            }
        }

        $this->setBreakpoints($breakpoints ?? []);
    }
}