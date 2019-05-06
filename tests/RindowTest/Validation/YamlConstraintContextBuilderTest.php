<?php
namespace RindowTest\Validation\YamlConstraintContextBuilderTest;

use PHPUnit\Framework\TestCase;
use Rindow\Stdlib\Entity\AbstractEntity;
use Rindow\Annotation\AnnotationManager;
use Rindow\Validation\Builder\YamlConstraintContextBuilder;
use Rindow\Validation\Core\ConstraintValidatorFactory;
use Rindow\Module\Yaml\Yaml;
use Rindow\Container\ModuleManager;

class Normal extends AbstractEntity
{
    protected $id;
}

class YamlError extends AbstractEntity
{
    protected $id;
}

class Test extends TestCase
{
    public static $skip = false;
    public static function setUpBeforeClass()
    {
        if (!Yaml::ready()) {
            self::$skip = true;
            return;
        }
    }

    public function setUp()
    {
        if(self::$skip)
            $this->markTestSkipped();
    }

    public function testNormal()
    {
    	$className = __NAMESPACE__.'\Normal';
        $config = array(
            'paths' => array(
            	__NAMESPACE__ => __DIR__.'/resources',
            ),
        );
    	$builder = new YamlConstraintContextBuilder();
        $builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
        $builder->setConfig($config);
        $builder->setYaml(new Yaml());
    	$result = $builder->getConstraints($className);
    	$this->assertEquals(1,count($result));

    	$this->assertEquals(1,count($result['id']));
    	$this->assertEquals('Rindow\Validation\Constraints\Max',
    		get_class($result['id'][0]->constraint));
    	$this->assertEquals(10,$result['id'][0]->constraint->value);
    }

    /**
     * @expectedException        Rindow\Validation\Exception\DomainException
     * @expectedExceptionMessage Yaml load error to validation for 
     */
    public function testYamlError()
    {
    	$className = __NAMESPACE__.'\YamlError';
        $config = array(
            'paths' => array(
                __NAMESPACE__ => __DIR__.'/resources',
            ),
        );
    	$builder = new YamlConstraintContextBuilder();
        $builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
        $builder->setConfig($config);
        $builder->setYaml(new Yaml());
    	$result = $builder->getConstraints($className);
    }

    public function testOnModule()
    {
        $className = __NAMESPACE__.'\Normal';
        $config = array(
            'module_manager' => array(
                'modules' => array(
                    'Rindow\Validation\Module' => true,
                    'Rindow\Stdlib\I18n\Module' => true,
                    'Rindow\Module\Yaml\Module' => true,
                ),
                'annotation_manager' => true,
                'enableCache' => false,
            ),
            'validator' => array(
                'builders' => array(
                    'annotation' => false,
                    'yaml' => array(
                        'paths' => array(
                            __NAMESPACE__ => __DIR__.'/resources',
                        ),
                    ),
                ),
            ),
        );
        $moduleManager = new ModuleManager($config);
        $sm = $moduleManager->getServiceLocator();
        $validator = $sm->get('Rindow\Validation\DefaultValidator');
        $product = new $className();
        $product->setId(11);
        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals('must be less than or equal to 10.',$violation['id'][0]->getMessage());
    }
}