<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class MaxTwoDecimalPlaces extends Constraint
{
    public string $message = 'The loan amount "{{ amount }}" have too much decimal places';

    #[MaxTwoDecimalPlaces]
    public function __construct(
        array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([], $groups, $payload);
    }
}