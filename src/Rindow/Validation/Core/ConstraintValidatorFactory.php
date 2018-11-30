<?php
namespace Rindow\Validation\Core;

use Rindow\Validation\Constraint;
use Rindow\Validation\ConstraintValidatorFactory as ConstraintValidatorFactoryInterface;
use Rindow\Validation\Exception;

class ConstraintValidatorFactory implements ConstraintValidatorFactoryInterface
{
    protected $constraintValidators;

    public function getConstraintValidator(Constraint $constraint)
    {
        if(isset($constraint->validatedBy))
            $validatorName = $constraint->validatedBy;
        else
            $validatorName = get_class($constraint).'Validator';
        $validator = $this->newInstance($validatorName);
        $validator->initialize($constraint);
        return $validator;
    }

    public function getInstance($validatorName)
    {
        if(isset($this->constraintValidators[$validatorName])) {
            return $this->constraintValidators[$validatorName];
        }
        if(!class_exists($validatorName))
            throw new Exception\DomainException('a class is not found:'.$validatorName);
        $validator = new $validatorName();
        $this->constraintValidators[$validatorName] = $validator;
        return $validator;
    }

    public function newInstance($validatorName)
    {
        if(!class_exists($validatorName))
            throw new Exception\DomainException('a class is not found:'.$validatorName);
        return new $validatorName();
    }
}