<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\ConstraintValidator;
use Rindow\Validation\Constraint as ConstraintInterface;
use Rindow\Validation\ConstraintValidatorContext;

class SizeValidator implements ConstraintValidator
{
    protected $min;
    protected $max;
    
    public function initialize(ConstraintInterface $constraint)
    {
        $this->min = $constraint->min;
        $this->max = $constraint->max;
    }

    public function isValid($value, ConstraintValidatorContext $context)
    {
        if($value===null || $value==='')
            return true;
        
        if(is_string($value)) {
            $size = strlen($value);
        } else if(is_array($value)) {
            $size = count($value);
        } else {
            return false;
        }

        if($this->max !== null && $size > $this->max)
            return false;
        if($this->min !== null && $size < $this->min)
            return false;
        return true;
    }
}
