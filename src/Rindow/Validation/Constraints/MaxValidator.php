<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\ConstraintValidator;
use Rindow\Validation\Constraint as ConstraintInterface;
use Rindow\Validation\ConstraintValidatorContext;

class MaxValidator implements ConstraintValidator
{
    protected $max;

    public function initialize(ConstraintInterface $constraint)
    {
        $this->max = $constraint->value;
    }

    public function isValid($value, ConstraintValidatorContext $context)
    {
        if($value===null || $value==='')
            return true;
        return ($value <= $this->max);
    }
}
