<?php
namespace Rindow\Validation;

interface ConstraintContextBuilder
{
	public function getConstraints($className);
    public function setConstraintValidatorFactory($constraintValidatorFactory);
    public function setAnnotationReader($annotationReader);
    public function getAnnotationReader();
    public function addNameSpace($nameSpace);
    public function setConfig(array $config=null);
}
