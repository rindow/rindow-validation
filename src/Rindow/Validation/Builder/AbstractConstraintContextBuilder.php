<?php
namespace Rindow\Validation\Builder;

use Rindow\Validation\ConstraintContextBuilder;
use Rindow\Validation\Constraint;
use Rindow\Validation\Constraints\CList;
use Rindow\Validation\Exception;
use Rindow\Annotation\AnnotationMetaData;
use ReflectionClass;

abstract class AbstractConstraintContextBuilder implements ConstraintContextBuilder
{
    protected $annotationReader;
    protected $constraintValidatorFactory;

    public function __construct($constraintValidatorFactory=null,$annotationReader=null)
    {
        if($constraintValidatorFactory)
            $this->setConstraintValidatorFactory($constraintValidatorFactory);

        if($annotationReader)
            $this->setAnnotationReader($annotationReader);
    }

    public function hasConstraintValidatorFactory()
    {
        return $this->constraintValidatorFactory ? true : false;
    }

    public function setConstraintValidatorFactory($constraintValidatorFactory)
    {
        $this->constraintValidatorFactory = $constraintValidatorFactory;
        return $this;
    }

    public function hasAnnotationReader()
    {
        return $this->annotationReader ? true : false;
    }

    public function getAnnotationReader()
    {
        if($this->annotationReader==null)
            throw new Exception\DomainException('AnnotationReader is not specified.');
        return $this->annotationReader;
    }

    public function setAnnotationReader($annotationReader)
    {
        $this->annotationReader = $annotationReader;
        return $this;
    }

    public function setConfig(array $config=null)
    {
        $this->config = $config;
        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }
    
    public function addNameSpace($nameSpace)
    {
        $this->getAnnotationReader()->addNameSpace($nameSpace);
    }

    protected function getConstraintContextList(array $constraintList,$parentConstaint=null)
    {
        $constraintContextList = array();
        foreach($constraintList as $constraint) {
            if($constraint instanceof CList) {
                $children = $constraint->value;
                $constraintContextList = array_merge(
                    $constraintContextList,
                    $this->getConstraintContextList($children));
                continue;
            }

            $children = $this->getChildrenConstraint($constraint);
            if($children) {
                $constraintContextList = array_merge(
                    $constraintContextList,
                    $this->getConstraintContextList($children,$constraint));
            }
            $constraintContext = new \stdClass();
            if($parentConstaint)
                $constraint->groups = $parentConstaint->groups;
            $constraintContext->constraint = $constraint;
            $constraintContext->validator = $this->constraintValidatorFactory->getConstraintValidator($constraint);
            $constraintContextList[] = $constraintContext;
        }
        return $constraintContextList;
    }

    protected function getChildrenConstraint($constraint)
    {
        $reader = $this->getAnnotationReader();
        if(method_exists($reader,'getMetaData'))
            $metaData = $reader->getMetaData($constraint);
        else
            $metaData = $this->generatetMetaData($constraint);

        if($metaData===false)
            return false;
        if($metaData->classAnnotations==null)
            return false;
        $children = array();
        foreach ($metaData->classAnnotations as $annotation) {
            if(!($annotation instanceof Constraint))
                continue;
            $children[] = clone $annotation;
        }
        if(count($children))
            return $children;
        else
            return false;
    }

    protected function generatetMetaData($constraint)
    {
        $reader = $this->getAnnotationReader();
        $ref = new ReflectionClass($constraint);
        $metaData = new AnnotationMetaData();
        $metaData->classAnnotations = $reader->getClassAnnotations($ref);
        return $metaData;
    }
}