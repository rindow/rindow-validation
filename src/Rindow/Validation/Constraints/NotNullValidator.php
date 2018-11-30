<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\ConstraintValidator;
use Rindow\Validation\Constraint as ConstraintInterface;
use Rindow\Validation\ConstraintValidatorContext;

class NotNullValidator implements ConstraintValidator
{
    public function initialize(ConstraintInterface $constraint)
    {
    }

    public function isValid($value, ConstraintValidatorContext $context)
    {
        return ($value !== null);
    }
}
