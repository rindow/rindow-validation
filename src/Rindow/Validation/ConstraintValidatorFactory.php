<?php
namespace Rindow\Validation;

interface ConstraintValidatorFactory
{
	public function getInstance($key);
}