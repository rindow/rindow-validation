<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\ConstraintValidator;
use Rindow\Validation\Constraint as ConstraintInterface;
use Rindow\Validation\ConstraintValidatorContext;

class MinValidator implements ConstraintValidator
{
    protected $min;
    
    public function initialize(ConstraintInterface $constraint)
    {
        $this->min = $constraint->value;
    }

    public function isValid($value, ConstraintValidatorContext $context)
    {
        if($value===null || $value==='')
            return true;
        return ($value >= $this->min);
    }
}
