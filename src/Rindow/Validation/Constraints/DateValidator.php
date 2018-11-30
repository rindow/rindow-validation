<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\ConstraintValidator;
use Rindow\Validation\Constraint as ConstraintInterface;
use Rindow\Validation\ConstraintValidatorContext;
use DateTime;

class DateValidator implements ConstraintValidator
{
    protected $format;

    public function initialize(ConstraintInterface $constraint)
    {
        $this->format  = $constraint->format;
    }

    public function isValid($value, ConstraintValidatorContext $context)
    {
        if($value===null || $value==='')
            return true;
        if($value instanceof DateTime)
            return true;

        return DateFormatValidationLibrary::isDate($value);
    }
}
