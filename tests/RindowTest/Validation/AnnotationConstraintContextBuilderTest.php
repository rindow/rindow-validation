<?php
namespace RindowTest\Validation\AnnotationConstraintContextBuilderTest;

use PHPUnit\Framework\TestCase;
use Rindow\Stdlib\Entity\AbstractEntity;
use Rindow\Annotation\AnnotationManager;
use Rindow\Validation\Core\ConstraintValidatorFactory;
use Rindow\Validation\Builder\AnnotationConstraintContextBuilder;
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
    /** @Max(10) **/
    protected $id;

    /**
    * @Max(100)
    * @Min(10)
    */
    protected $id2;

    protected $stock;
}

class FunctionValidation extends AbstractEntity
{
    /** @Max(10) **/
    public function id() {}

    /**
    * @Max(100)
    * @Min(10)
    */
    public function id2() {}

    public function stock() {}
}

class CListValidation extends AbstractEntity
{
    /** @Max(10) **/
    public function id() {}

    /**
    * @CList({
    * 	@Max(100),
    * 	@Max(10)
    * })
    */
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
    /** @ChildMax() **/
    protected $id;

    /**
    * @Max(100)
    * @Min(10)
    */
    protected $id2;

    protected $stock;
}

/**
 * @GroupSequence({"a","b"})
 */
class GroupSequenceValidation extends AbstractEntity
{
    /** @Max(value=10,groups={"a"}) **/
    protected $id;
    /** @Max(value=100,groups={"b"}) **/
    protected $id2;
}

class Test extends TestCase
{
    public function setUp()
    {
    }

    public function testNormal()
    {
    	$className = __NAMESPACE__.'\Normal';
    	$builder = new AnnotationConstraintContextBuilder();
    	$builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
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
    	$builder = new AnnotationConstraintContextBuilder();
        $builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
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
    	$builder = new AnnotationConstraintContextBuilder();
        $builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
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
    	$builder = new AnnotationConstraintContextBuilder();
        $builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
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
    	$builder = new AnnotationConstraintContextBuilder();
        $builder->setConstraintValidatorFactory(new ConstraintValidatorFactory());
        $builder->setAnnotationReader(new AnnotationManager());
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
}