<?php
namespace RindowTest\Validation\ArrayConstraintContextBuilderTest;

use PHPUnit\Framework\TestCase;
use Rindow\Stdlib\Entity\AbstractEntity;
use Rindow\Annotation\AnnotationManager;
use Rindow\Validation\Core\ConstraintValidatorFactory;
use Rindow\Validation\Builder\ArrayConstraintContextBuilder;
use Rindow\Validation\ConstraintValidator;
use Rindow\Validation\Constraint as ConstraintInterface;
use Rindow\Validation\ConstraintValidatorContext;
use Rindow\Validation\Core\AbstractConstraint;
use Rindow\Validation\Constraints\Max;
use Rindow\Validation\Constraints\Min;
use Rindow\Validation\Constraints\CList;
use Rindow\Validation\Constraints\Constraint;
use Rindow\Validation\Constraints\GroupSequence;

class Normal extends AbstractEntity
{
    protected $id;

    protected $id2;

    protected $stock;
}

class FunctionValidation extends AbstractEntity
{
    public function id() {}

    public function id2() {}

    public function stock() {}
}

class CListValidation extends AbstractEntity
{
    public function id() {}

    public function id2() {}

    public function stock() {}
}

/**
 * @Annotation
 * @Target({ METHOD, FIELD, ANNOTATION_TYPE })
 * @Constraint(validatedBy = {})
 * @Max(10)
 */
class ChildMax extends AbstractConstraint
{
    public $message = "must be false.";
    public $groups = array();
    public $payload = array();
}
class ChildMaxValidator implements ConstraintValidator
{
    public function initialize(ConstraintInterface $constraint)
    {
    }

    public function isValid($value, ConstraintValidatorContext $context)
    {
        return true;
    }
}

class ChildConstraintValidation extends AbstractEntity
{
    protected $id;

    protected $id2;

    protected $stock;
}

class GroupSequenceValidation extends AbstractEntity
{
    protected $id;
    protected $id2;
}

class Test extends TestCase
{
    public function setUp()
    {
        usleep( RINDOW_TEST_CLEAR_CACHE_INTERVAL );
        \Rindow\Stdlib\Cache\CacheFactory::clearCache();
        usleep( RINDOW_TEST_CLEAR_CACHE_INTERVAL );
    }

    public function testNormal()
    {
    	$className = __NAMESPACE__.'\Normal';
        $config = array(
            'mapping' => array(
                $className => array(
                    'properties' => array(
                        'id' => array(
                            'Max' => 10,
                        ),
                        'id2' => array(
                            'Max' => 100,
                            'Min' => 10,
                        ),
                    ),
                ),
            ),
        );
    	$builder = new ArrayConstraintContextBuilder();
        $builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
        $builder->setConfig($config);
    	$result = $builder->getConstraints($className);
    	$this->assertEquals(2,count($result));

    	$this->assertEquals(1,count($result['id']));
    	$this->assertEquals('Rindow\Validation\Constraints\Max',
    		get_class($result['id'][0]->constraint));
    	$this->assertEquals(10,$result['id'][0]->constraint->value);

    	$this->assertEquals(2,count($result['id2']));
    	$this->assertEquals('Rindow\Validation\Constraints\Max',
    		get_class($result['id2'][0]->constraint));
    	$this->assertEquals(100,$result['id2'][0]->constraint->value);
    	$this->assertEquals('Rindow\Validation\Constraints\Min',
    		get_class($result['id2'][1]->constraint));
    	$this->assertEquals(10,$result['id2'][1]->constraint->value);
    }

    public function testFunctionValidation()
    {
    	$className = __NAMESPACE__.'\FunctionValidation';
        $config = array(
            'mapping' => array(
                $className => array(
                    'properties' => array(
                        'id' => array(
                            'Max' => 10,
                        ),
                        'id2' => array(
                            'Max' => 100,
                            'Min' => 10,
                        ),
                    ),
                ),
            ),
        );
        $builder = new ArrayConstraintContextBuilder();
        $builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
        $builder->setConfig($config);
    	$result = $builder->getConstraints($className);
    	$this->assertEquals(2,count($result));

    	$this->assertEquals(1,count($result['id']));
    	$this->assertEquals('Rindow\Validation\Constraints\Max',
    		get_class($result['id'][0]->constraint));
    	$this->assertEquals(10,$result['id'][0]->constraint->value);

    	$this->assertEquals(2,count($result['id2']));
    	$this->assertEquals('Rindow\Validation\Constraints\Max',
    		get_class($result['id2'][0]->constraint));
    	$this->assertEquals(100,$result['id2'][0]->constraint->value);
    	$this->assertEquals('Rindow\Validation\Constraints\Min',
    		get_class($result['id2'][1]->constraint));
    	$this->assertEquals(10,$result['id2'][1]->constraint->value);
    }

    public function testCListValidation()
    {
    	$className = __NAMESPACE__.'\CListValidation';
        $config = array(
            'mapping' => array(
                $className => array(
                    'properties' => array(
                        'id' => array(
                            'Max' => 10,
                        ),
                        'id2' => array(
                            'CList' => array(
                                array('Max' => 100),
                                array('Max' => 10),
                            ),
                        ),
                    ),
                ),
            ),
        );
        $builder = new ArrayConstraintContextBuilder();
        $builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
        $builder->setConfig($config);
    	$result = $builder->getConstraints($className);
    	$this->assertEquals(2,count($result));

    	$this->assertEquals(1,count($result['id']));
    	$this->assertEquals('Rindow\Validation\Constraints\Max',
    		get_class($result['id'][0]->constraint));
    	$this->assertEquals(10,$result['id'][0]->constraint->value);

    	$this->assertEquals(2,count($result['id2']));
    	$this->assertEquals('Rindow\Validation\Constraints\Max',
    		get_class($result['id2'][0]->constraint));
    	$this->assertEquals(100,$result['id2'][0]->constraint->value);
    	$this->assertEquals('Rindow\Validation\Constraints\Max',
    		get_class($result['id2'][1]->constraint));
    	$this->assertEquals(10,$result['id2'][1]->constraint->value);
    }

    public function testChildConstraint()
    {
    	$className = __NAMESPACE__.'\ChildConstraintValidation';
        $config = array(
            'mapping' => array(
                $className => array(
                    'constraint_namespaces' => array(
                        'Rindow\Validation\Constraints',
                        __NAMESPACE__,
                    ),
                    'properties' => array(
                        'id' => array(
                            'Max' => 10,
                            'ChildMax' => null,
                        ),
                        'id2' => array(
                            'Max' => 100,
                            'Min' => 10,
                        ),
                    ),
                ),
            ),
        );
        $builder = new ArrayConstraintContextBuilder();
        $builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
        $builder->setConfig($config);
    	$result = $builder->getConstraints($className);
    	$this->assertEquals(2,count($result));

    	$this->assertEquals(2,count($result['id']));
    	$this->assertEquals('Rindow\Validation\Constraints\Max',
    		get_class($result['id'][0]->constraint));
    	$this->assertEquals(10,$result['id'][0]->constraint->value);
    	$this->assertEquals(__NAMESPACE__.'\ChildMax',
    		get_class($result['id'][1]->constraint));

    	$this->assertEquals(2,count($result['id2']));
    	$this->assertEquals('Rindow\Validation\Constraints\Max',
    		get_class($result['id2'][0]->constraint));
    	$this->assertEquals(100,$result['id2'][0]->constraint->value);
    	$this->assertEquals('Rindow\Validation\Constraints\Min',
    		get_class($result['id2'][1]->constraint));
    	$this->assertEquals(10,$result['id2'][1]->constraint->value);
    }

    public function testGroupSequence()
    {
    	$className = __NAMESPACE__.'\GroupSequenceValidation';
        $config = array(
            'mapping' => array(
                $className => array(
                    'type' => array(
                        'GroupSequence' => array('a','b'),
                    ),
                    'properties' => array(
                        'id' => array(
                            'Max' => array('value'=>10,'groups'=>array("a")),
                        ),
                        'id2' => array(
                            'Max' => array('value'=>100,'groups'=>array("b")),
                        ),
                    ),
                ),
            ),
        );
        $builder = new ArrayConstraintContextBuilder();
        $builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
        $builder->setConfig($config);
    	$result = $builder->getConstraints($className);
    	$this->assertEquals(3,count($result));

    	$this->assertEquals(1,count($result['__CLASS__']));
    	$this->assertEquals('Rindow\Validation\Constraints\GroupSequence',
    		get_class($result['__CLASS__']['Rindow\Validation\Constraints\GroupSequence']));
    	$this->assertEquals(array('a','b'),$result['__CLASS__']['Rindow\Validation\Constraints\GroupSequence']->value);

    	$this->assertEquals(1,count($result['id']));
    	$this->assertEquals('Rindow\Validation\Constraints\Max',
    		get_class($result['id'][0]->constraint));
    	$this->assertEquals(10,$result['id'][0]->constraint->value);
        $this->assertEquals(array('a'),$result['id'][0]->constraint->groups);

    	$this->assertEquals(1,count($result['id2']));
    	$this->assertEquals('Rindow\Validation\Constraints\Max',
    		get_class($result['id2'][0]->constraint));
    	$this->assertEquals(100,$result['id2'][0]->constraint->value);
        $this->assertEquals(array('b'),$result['id2'][0]->constraint->groups);
    }

    /**
     * @expectedException        Rindow\Validation\Exception\DomainException
     * @expectedExceptionMessage Invalid setting of constraint property on "RindowTest\Validation\ArrayConstraintContextBuilderTest\Normal::id"
     */
    public function testInvalidConstraintPropertyOnField()
    {
        $className = __NAMESPACE__.'\Normal';
        $config = array(
            'mapping' => array(
                $className => array(
                    'properties' => array(
                        'id' => array(
                            'Max' => array('none'=>10),
                        ),
                    ),
                ),
            ),
        );
        $builder = new ArrayConstraintContextBuilder();
        $builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
        $builder->setConfig($config);
        $result = $builder->getConstraints($className);
    }

    /**
     * @expectedException        Rindow\Validation\Exception\DomainException
     * @expectedExceptionMessage Invalid setting of constraint property on "RindowTest\Validation\ArrayConstraintContextBuilderTest\Normal"
     */
    public function testInvalidConstraintPropertyOnType()
    {
        $className = __NAMESPACE__.'\Normal';
        $config = array(
            'mapping' => array(
                $className => array(
                    'type' => array(
                        'GroupSequence' => array('none'=>array('a','b')),
                    ),
                    'properties' => array(
                        'id' => array(
                            'Max' => 10,
                        ),
                    ),
                ),
            ),
        );
        $builder = new ArrayConstraintContextBuilder();
        $builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
        $builder->setConfig($config);
        $result = $builder->getConstraints($className);
    }

    /**
     * @expectedException        Rindow\Validation\Exception\DomainException
     * @expectedExceptionMessage Unknown constraint "None" on "RindowTest\Validation\ArrayConstraintContextBuilderTest\Normal::id"
     */
    public function testUnknownConstraintOnField()
    {
        $className = __NAMESPACE__.'\Normal';
        $config = array(
            'mapping' => array(
                $className => array(
                    'properties' => array(
                        'id' => array(
                            'None' => 10,
                        ),
                    ),
                ),
            ),
        );
        $builder = new ArrayConstraintContextBuilder();
        $builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
        $builder->setConfig($config);
        $result = $builder->getConstraints($className);
    }

    /**
     * @expectedException        Rindow\Validation\Exception\DomainException
     * @expectedExceptionMessage Unknown constraint "None" on "RindowTest\Validation\ArrayConstraintContextBuilderTest\Normal"
     */
    public function testUnknownConstraintOnType()
    {
        $className = __NAMESPACE__.'\Normal';
        $config = array(
            'mapping' => array(
                $className => array(
                    'type' => array(
                        'None' => array('a'),
                    ),
                    'properties' => array(
                        'id' => array(
                            'Max' => 10,
                        ),
                    ),
                ),
            ),
        );
        $builder = new ArrayConstraintContextBuilder();
        $builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
        $builder->setConfig($config);
        $result = $builder->getConstraints($className);
    }

    /**
     * @expectedException        Rindow\Validation\Exception\DomainException
     * @expectedExceptionMessage value of "CList" must be list of constraint on "RindowTest\Validation\ArrayConstraintContextBuilderTest\Normal::id"
     */
    public function testInvalidCList1()
    {
        $className = __NAMESPACE__.'\Normal';
        $config = array(
            'mapping' => array(
                $className => array(
                    'properties' => array(
                        'id' => array(
                            'CList' => 10,
                        ),
                    ),
                ),
            ),
        );
        $builder = new ArrayConstraintContextBuilder();
        $builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
        $builder->setConfig($config);
        $result = $builder->getConstraints($className);
    }


    /**
     * @expectedException        Rindow\Validation\Exception\DomainException
     * @expectedExceptionMessage value of "CList" must be list of constraint on "RindowTest\Validation\ArrayConstraintContextBuilderTest\Normal::id"
     */
    public function testInvalidCList2()
    {
        $className = __NAMESPACE__.'\Normal';
        $config = array(
            'mapping' => array(
                $className => array(
                    'properties' => array(
                        'id' => array(
                            'CList' => array(),
                        ),
                    ),
                ),
            ),
        );
        $builder = new ArrayConstraintContextBuilder();
        $builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
        $builder->setConfig($config);
        $result = $builder->getConstraints($className);
    }
}