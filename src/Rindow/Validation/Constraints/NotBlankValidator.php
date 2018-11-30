<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\ConstraintValidator;
use Rindow\Validation\Constraint as ConstraintInterface;
use Rindow\Validation\ConstraintValidatorContext;

class NotBlankValidator implements ConstraintValidator
{
    public function initialize(ConstraintInterface $constraint)
    {
    }

    public function isValid($value, ConstraintValidatorContext $context)
    {
        if($value===null)
            return true;
        if(!is_string($value))
            return true;
        return (strlen($value) > 0);
    }
}
