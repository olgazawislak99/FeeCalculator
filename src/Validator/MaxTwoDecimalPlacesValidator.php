<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MaxTwoDecimalPlacesValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof MaxTwoDecimalPlaces) {
            throw new UnexpectedTypeException($constraint, MaxTwoDecimalPlaces::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $decimalPlaces = explode('.', $value);

        if (isset($decimalPlaces[1]) && strlen($decimalPlaces[1]) > 2) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ amount }}', $value)
                ->addViolation();
        }
    }
}