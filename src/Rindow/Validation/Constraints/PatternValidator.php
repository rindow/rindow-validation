<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\ConstraintValidator;
use Rindow\Validation\Constraint as ConstraintInterface;
use Rindow\Validation\ConstraintValidatorContext;

class PatternValidator implements ConstraintValidator
{
    protected $regexp;
    
    public function initialize(ConstraintInterface $constraint)
    {
        $pregPattern = false;
        if(preg_match('@^/[^/]+/[a-zA-Z]*$@', $constraint->regexp) )
            $pregPattern = true;
        else if(preg_match('/^@[^@]+@[a-zA-Z]*$/', $constraint->regexp) )
            $pregPattern = true;
        else if(preg_match('/^#[^#]+#[a-zA-Z]*$/', $constraint->regexp) )
            $pregPattern = true;

        if($pregPattern) {
            $this->regexp = $constraint->regexp;
            return;
        }

        $pregFlags = '';
        if($constraint->flags) {
            if(is_array($constraint->flags))
                $flags = $constraint->flags;
            else
                $flags = array($constraint->flags);
            foreach($flags as $flag) {
                if($flag==Pattern::CASE_INSENSITIVE)
                    $pregFlags .= 'i';
                else if($flag==Pattern::MULTILINE)
                    $pregFlags .= 'm';
                else if($flag==Pattern::DOTALL)
                    $pregFlags .= 's';
                else
                    throw new Exception\DomainException('Invalid flag:',$flag);
            }
        }

        if(strpos($constraint->regexp,'/')===false)
            $this->regexp = '/'.$constraint->regexp.'/';
        else if(strpos($constraint->regexp,'@')===false)
            $this->regexp = '@'.$constraint->regexp.'@';
        else
            $this->regexp = '#'.$constraint->regexp.'#';

        $this->regexp .= $pregFlags;
    }

    public function isValid($value, ConstraintValidatorContext $context)
    {
        if($value===null || $value==='')
            return true;

        $res = preg_match($this->regexp,$value);
        return ($res!==false && $res>0);
    }
}
