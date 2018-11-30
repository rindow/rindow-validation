<?php
namespace Rindow\Validation;

interface ConstraintValidator
{
    public function initialize(Constraint $constraint);

  	public function isValid($value, ConstraintValidatorContext $context);
}