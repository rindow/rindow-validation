<?php
namespace Rindow\Validation\Core;

use Rindow\Annotation\AnnotationManager;
use Rindow\Stdlib\Cache\CacheHandlerTemplate;

use ReflectionClass;
use Rindow\Annotation\ElementType;
use Rindow\Annotation\AnnotationMetaData;
use Rindow\Validation\ConstraintManager;
use Rindow\Validation\ConstraintContextBuilder;
use Rindow\Validation\Constraints\CList;
use Rindow\Validation\Builder\AnnotationConstraintContextBuilder;
use Rindow\Validation\Builder\ArrayConstraintContextBuilder;
use Rindow\Validation\Exception;

class ConstraintContextManager implements ConstraintManager
{
    const ANNOTATION_CONSTRAINT_CONTEXT_BUILDER = 'Rindow\\Validation\\Builder\\AnnotationConstraintContextBuilder';
    const ARRAY_CONSTRAINT_CONTEXT_BUILDER = 'Rindow\\Validation\\Builder\\ArrayConstraintContextBuilder';
    const YAML_CONSTRAINT_CONTEXT_BUILDER = 'Rindow\\Validation\\Builder\\YamlConstraintContextBuilder';

    protected $cacheHandler;
    protected $builders = array();
    protected $serviceLocator;
    protected $annotationReader;
    protected $builderAliases = array(
        'annotation' => self::ANNOTATION_CONSTRAINT_CONTEXT_BUILDER,
        'array' => self::ARRAY_CONSTRAINT_CONTEXT_BUILDER,
        'yaml' => self::YAML_CONSTRAINT_CONTEXT_BUILDER
    );

    public function __construct(
        $constraintValidatorFactory=null,
        $serviceLocator=null,
        $annotationReader=null)
    {
        $this->cacheHandler = new CacheHandlerTemplate(__CLASS__);
        if($constraintValidatorFactory)
            $this->constraintValidatorFactory = $constraintValidatorFactory;
        else
            $this->constraintValidatorFactory = new ConstraintValidatorFactory();
        $this->serviceLocator =$serviceLocator;
        $this->annotationReader = $annotationReader;
    }

    public function getConstraintsCache()
    {
        return $this->cacheHandler->getCache('constraints');
    }

    public function setEnableCache($enableCache=true)
    {
        $this->cacheHandler->setEnableCache($enableCache);
    }

    public function setCachePath($cachePath)
    {
        $this->cacheHandler->setCachePath($cachePath);
    }

    public function setConfig(array $config=null)
    {
        $this->config = $config;
        if(isset($config['builder_aliases']))
            $this->builderAliases = $config['builder_aliases'];
        if($config['builders']) {
            foreach ($config['builders'] as $name => $option) {
                if($option===false)
                    continue;
                if(isset($this->builderAliases[$name])) {
                    $name = $this->builderAliases[$name];
                }
                $builder = $this->getBuilderInstance($name);
                $this->addBuilder($builder);
                $builder->setConfig($option);
            }
        }
    }

    protected function getBuilderInstance($serviceName)
    {
        if($this->serviceLocator) {
            return $this->serviceLocator->get($serviceName);
        }
        if(!class_exists($serviceName))
            throw new Exception\DomainException('constraint context builder"'.$name.'" is not found.');
        return new $serviceName($this->constraintValidatorFactory,$this->annotationReader);
    }

    public function addBuilder(ConstraintContextBuilder $builder)
    {
        if(!$builder->hasConstraintValidatorFactory())
            $builder->setConstraintValidatorFactory($this->constraintValidatorFactory);
        if(!$builder->hasAnnotationReader())
            $builder->setAnnotationReader($this->annotationReader);
        $this->builders[] = $builder;
    }

    public function getBuilders()
    {
        if(count($this->builders))
            return $this->builders;
        $this->addBuilder(new AnnotationConstraintContextBuilder($this->constraintValidatorFactory,$this->annotationReader));
        return $this->builders;
    }
    
    public function getAnnotationReader()
    {
        return $this->annotationReader;
    }
    
    public function setAnnotationReader($annotationReader)
    {
        $this->annotationReader = $annotationReader;
        foreach($this->getBuilders() as $builder) {
            $builder->setAnnotationReader($annotationReader);
        }
    }
/*
    public function getConstraints($className)
    {
        $constraintsCache = $this->getConstraintsCache();
        if(isset($constraintsCache[$className]))
            return $constraintsCache[$className];

        foreach($this->getBuilders() as $builder) {
            $constraints = $builder->getConstraints($className);
            if($constraints)
                break;
        }
        $constraintsCache[$className] = $constraints;
        return $constraints;
    }
*/
    public function getConstraints($className)
    {
        $constraintsCache = $this->getConstraintsCache();
        $manager = $this;
        $constraints = $constraintsCache->get(
            $className,
            false,
            function ($cache,$className,&$entry) use ($manager) {
                $constraints = false;
                foreach($manager->getBuilders() as $builder) {
                    $constraints = $builder->getConstraints($className);
                    if($constraints) {
                        $entry = $constraints;
                        return true;
                    }
                }
                $entry = array();
                return true;
            }
        );
        return $constraints;
    }
}