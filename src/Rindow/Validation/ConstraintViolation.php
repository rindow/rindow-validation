<?php
namespace Rindow\Validation;

interface ConstraintViolation {

	public function getMessage();

	public function getMessageTemplate();

	public function getRootBean();

	public function getPropertyPath();

	public function getInvalidValue();
}