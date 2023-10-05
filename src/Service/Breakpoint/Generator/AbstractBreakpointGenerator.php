<?php

namespace App\Service\Breakpoint\Generator;

use App\Entity\Enum\LoanDuration;
use App\Exception\BreakpointFileNotFoundException;
use App\Exception\WrongFileExtensionException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractBreakpointGenerator
{
    const EXTENSION = '';

    public function __construct(
        protected readonly ValidatorInterface $validator,
        protected readonly string             $filePath,
    )
    {
    }

    private array $breakpoints = [];

    abstract public function generateBreakpoints(LoanDuration $loanDuration): void;

    public function getBreakpoints(): array
    {
        return $this->breakpoints;
    }

    protected function setBreakpoints(array $breakpoints): void
    {
        $this->breakpoints = $breakpoints;
    }

    /**
     * @throws BreakpointFileNotFoundException
     */
    protected function getFile(): array
    {
        if(!file_exists($this->filePath)) {
            throw new BreakpointFileNotFoundException;
        }

        return file($this->filePath);
    }

    /**
     * @throws WrongFileExtensionException
     */
    protected function checkFileExtension(string $expectedExtension): void
    {
        $fileParts = pathinfo($this->filePath);

        if (!isset($fileParts['extension']) || ($fileParts['extension']) !== $expectedExtension)
        {
            throw new WrongFileExtensionException($fileParts['extension'], $expectedExtension);
        }
    }

}