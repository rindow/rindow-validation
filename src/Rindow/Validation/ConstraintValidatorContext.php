<?php
namespace Rindow\Validation;

interface ConstraintValidatorContext
{
    public function getConstraint();

    public function disableDefaultConstraintViolation();

    public function getDefaultConstraintMessageTemplate();

    public function setMessageTemplate($messageTemplate);

    public function setPropertyPath($name);

    public function addConstraintViolation();
}
