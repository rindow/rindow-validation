<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\ConstraintValidator;
use Rindow\Validation\Constraint as ConstraintInterface;
use Rindow\Validation\ConstraintValidatorContext;

class DigitsValidator implements ConstraintValidator
{
    protected $integer;
    protected $fraction;

    public function initialize(ConstraintInterface $constraint)
    {
        $this->integer  = $constraint->integer;
        $this->fraction = $constraint->fraction;
    }

    public function isValid($value, ConstraintValidatorContext $context)
    {
        if($value===null || $value==='')
            return true;
        $parts = explode('.', $value);
        if(count($parts)>2)
            return false;
        $integerPartLen = strlen($parts[0]);
        if($integerPartLen>0 && !ctype_digit($parts[0]))
            return false;
        if($integerPartLen > $this->integer)
            return false;

        if(!isset($parts[1]))
            return true;
        $fractionPartLen = strlen($parts[1]);
        if($fractionPartLen>0 && !ctype_digit($parts[1]))
            return false;
        if($fractionPartLen > $this->fraction)
            return false;
        return true;
    }
}
