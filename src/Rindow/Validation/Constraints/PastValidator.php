<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\ConstraintValidator;
use Rindow\Validation\Constraint as ConstraintInterface;
use Rindow\Validation\ConstraintValidatorContext;
use DateTime;

class PastValidator implements ConstraintValidator
{
    public function initialize(ConstraintInterface $constraint)
    {
    }

    public function isValid($value, ConstraintValidatorContext $context)
    {
        if($value===null || $value==='')
            return true;

        $value = DateFormatValidationLibrary::convertToDateTime($value);
        if($value===false)
            return false;

        $interval = $value->diff(new DateTime('now'));

        return ($interval->invert==0);
    }
}
