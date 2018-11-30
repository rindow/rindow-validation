<?php
namespace Rindow\Validation\Builder;

use Rindow\Validation\Constraint;
use Rindow\Validation\Constraints\CList;
use Rindow\Validation\Exception;
use Rindow\Stdlib\Entity\Exception\DomainException as EntityException;

class ArrayConstraintContextBuilder extends AbstractConstraintContextBuilder
{
    const DEFAULT_CONSTRAINT_NAMESPACE = 'Rindow\\Validation\\Constraints';

    protected $serviceLocator;

    public function getConstraints($className)
    {
        $definition = $this->loadDefinition($className);
        if(!$definition)
            return array();
        if(isset($definition['constraint_namespaces']))
            $namespaces = $definition['constraint_namespaces'];
        else
            $namespaces = array(self::DEFAULT_CONSTRAINT_NAMESPACE);
        $constraints = array();
        if(isset($definition['type'])) {
            if(!is_array($definition['type']))
                throw new Exception\DomainException('type must be array in validation definition for "'.$className.'"');
            foreach ($definition['type'] as $constraintName => $properties) {
                try {
                    $constraint = $this->matchesConstraint($constraintName,$properties,$namespaces);
                    if(!$constraint)
                        throw new Exception\DomainException('Unknown constraint "'.$constraintName.'" on "'.$className.'"');
                    $constraints['__CLASS__'][get_class($constraint)] = $constraint;
                } catch(EntityException $e) {
                    throw new Exception\DomainException('Invalid setting of constraint property on "'.$className.'"',0,$e);
                } catch(Exception\DomainException $e) {
                    throw new Exception\DomainException($e->getMessage().' on "'.$className,0,$e);
                }
            }
        }
        if(isset($definition['properties'])) {
            if(!is_array($definition['properties']))
                throw new Exception\DomainException('properties must be array in validation definition for "'.$className.'"');
            foreach($definition['properties'] as $field => $constraintDefinitions) {
                try {
                    $constraintList = array();
                    foreach ($constraintDefinitions as $constraintName => $properties) {
                        $constraint = $this->matchesConstraint($constraintName,$properties,$namespaces);
                        if(!$constraint)
                            throw new Exception\DomainException('Unknown constraint "'.$constraintName.'" on "'.$className.'::'.$field.'"');
                        $constraintList[] = $constraint;
                    }
                    $constraints[$field] = $this->getConstraintContextList($constraintList);
                } catch(EntityException $e) {
                    throw new Exception\DomainException('Invalid setting of constraint property on "'.$className.'::'.$field.'"',0,$e);
                } catch(Exception\DomainException $e) {
                    throw new Exception\DomainException($e->getMessage().' on "'.$className.'::'.$field.'"',0,$e);
                }
            }
        }
        return $constraints;
    }

    protected function matchesConstraint($constraintName,$properties,$namespaces)
    {
        foreach ($namespaces as $namespace) {
            $className = $namespace.'\\'.$constraintName;
            if(!class_exists($className))
                continue;
            $constraint = new $className();
            if(!($constraint instanceof Constraint))
                continue;
            if(is_array($properties) && !isset($properties[0]) && count($properties)) {
                foreach ($properties as $key => $value) {
                    if($key == 'value' && $constraint instanceof CList) {
                        $constraint->value = $this->getConstraintListFromCList($value,$namespaces);
                    } else {
                        $constraint->$key = $value;
                    }
                }
            } else {
                if($properties!==null || property_exists($constraint, 'value')) {
                    if($constraint instanceof CList) {
                        $constraint->value = $this->getConstraintListFromCList($properties,$namespaces);
                    } else {
                        $constraint->value = $properties;
                    }
                }
            }
            return $constraint;
        }
        return false;
    }

    protected function getConstraintListFromCList($value,$namespaces)
    {
        if(!is_array($value) || !array_key_exists(0, $value))
            throw new Exception\DomainException('value of "CList" must be list of constraint');
        $constraintList = array();
        foreach ($value as $item) {
            foreach ($item as $constraintName => $properties) {
                if(is_numeric($constraintName))
                    throw new Exception\DomainException('value of "CList" must be list of constraint');
                $constraintList[] = $this->matchesConstraint($constraintName,$properties,$namespaces);
            }
        }
        return $constraintList;
    }

    protected function loadDefinition($className)
    {
        $config = $this->getConfig();
        if(!isset($config['mapping'][$className]))
            return false;
        return $config['mapping'][$className];
    }
}