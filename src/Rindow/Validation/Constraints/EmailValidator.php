<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\ConstraintValidator;
use Rindow\Validation\Constraint as ConstraintInterface;
use Rindow\Validation\ConstraintValidatorContext;

class EmailValidator implements ConstraintValidator
{
    public function initialize(ConstraintInterface $constraint)
    {
    }

    public function isValid($value, ConstraintValidatorContext $context)
    {
        if($value===null || $value==='')
            return true;

        $parts = explode('@',$value);
        if(count($parts)!=2)
            return false;
        if(preg_match('/^[a-zA-Z][a-zA-Z0-9\.\-_]*[a-zA-Z0-9]$/', $parts[0])==0)
            return false;
        $domain = explode('.', $parts[1]);
        if(count($domain)<2)
            return false;
        $topidx = count($domain)-1;
        foreach($domain as $idx => $element) {
            if($idx==$topidx) {
                if(preg_match('/^[a-zA-Z]+$/', $element)==0)
                    return false;
            } else if($idx==$topidx-1) {
                if(preg_match('/^[a-zA-Z][a-zA-Z0-9\-_]*[a-zA-Z0-9]$/', $element)==0)
                    return false;
            } else {
                if(preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\-_]*$/', $element)==0)
                    return false;
            }
        }
        return true;
    }
}
