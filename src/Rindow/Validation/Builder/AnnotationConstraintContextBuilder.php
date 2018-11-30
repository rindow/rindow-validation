<?php
namespace Rindow\Validation\Builder;

use ReflectionClass;
use Rindow\Annotation\ElementType;
use Rindow\Validation\Constraint;

class AnnotationConstraintContextBuilder extends AbstractConstraintContextBuilder
{
    public function getConstraints($className)
    {
        $reader = $this->getAnnotationReader();
        $constraints = array();
        $classRef = new ReflectionClass($className);
        $annotations = $reader->getClassAnnotations($classRef);
        foreach ($annotations as $annotation) {
            if($annotation instanceof Constraint)
                $constraints['__CLASS__'][get_class($annotation)] = $annotation;
        }
        $this->getConstraintsByTarget(
            $constraints,
            $classRef->getProperties(),
            ElementType::FIELD
        );
        $this->getConstraintsByTarget(
            $constraints,
            $classRef->getMethods(),
            ElementType::METHOD
        );

        return $constraints;
    }

    protected function getConstraintsByTarget(&$constraints,$reflections,$elementType)
    {
        $reader = $this->getAnnotationReader();
        foreach($reflections as $ref) {
            if($elementType==ElementType::FIELD) {
                $annotations = $reader->getPropertyAnnotations($ref);
            }
            else if($elementType==ElementType::METHOD) {
                $annotations = $reader->getMethodAnnotations($ref);
            }
            if(count($annotations)==0)
                continue;
            $constraintList = array();
            foreach ($annotations as $annotation) {
                if($annotation instanceof Constraint)
                    $constraintList[] = $annotation;
            }
            $constraints[$ref->getName()] = $this->getConstraintContextList($constraintList);
        }
    }
}