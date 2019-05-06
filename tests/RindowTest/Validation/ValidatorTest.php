<?php
namespace RindowTest\Validation\ValidatorTest;

use PHPUnit\Framework\TestCase;
use Rindow\Stdlib\Entity\AbstractEntity;
use Rindow\Stdlib\Entity\PropertyAccessPolicy;
use Rindow\Stdlib\I18n\Translator;
use Rindow\Stdlib\Cache\ConfigCache\ConfigCacheFactory;
use Rindow\Container\ModuleManager;
use Rindow\Annotation\AnnotationManager;

// Test Target Classes
use Rindow\Validation\Core\AbstractConstraint;
use Rindow\Validation\Core\Validator;
use Rindow\Validation\Core\ConstraintContextManager;
use Rindow\Validation\Constraint as ConstraintInterface;
use Rindow\Validation\ConstraintValidator;
use Rindow\Validation\ConstraintValidatorContext;

use Rindow\Validation\Constraints\AssertTrue;
use Rindow\Validation\Constraints\CList;
use Rindow\Validation\Constraints\Constraint;
use Rindow\Validation\Constraints\Max;
use Rindow\Validation\Constraints\Min;
use Rindow\Validation\Constraints\NotNull;
use Rindow\Validation\Constraints\Size;
use Rindow\Validation\Constraints\GroupSequence;

class Product extends AbstractEntity
{
    /** @Max(10) **/
    protected $id;
    /** @Min(10) **/
    protected $id2;
    /** @Max(100) **/
    protected $stock;
}

class Product2 extends AbstractEntity
{
    /** @Min(10) **/
    protected $id;
    /** @Max(value=100, message="stock max is {value}.") **/
    protected $stock;
}

class Product3 extends AbstractEntity
{
    /** @Max(10) **/
    protected $id;
    /** @Size(max=10, message="name max length is {max}.") **/
    protected $name;
    /** @Size(min=5, message="description min length is {min}.") **/
    protected $description;
    /** @Size(min=5, max=10, message="code length is {min} - {max}.") **/
    protected $code;
}

class Product4 extends AbstractEntity
{
    /** @Max(10) @NotNull **/
    protected $id;
    /** @Max(100)  @NotNull **/
    protected $stock;
}

class Product5 extends AbstractEntity
{
    /** @Max(10) **/
    protected $id;
    /** @Size(min=8) **/
    protected $password;
    /** @Size(min=8) **/
    protected $passwordAgain;
    /** @AssertTrue(message="Passwords do not match.",path="password") **/
    public function comparePassword()
    {
        return ($this->password == $this->passwordAgain);
    }
}

class Product6 implements PropertyAccessPolicy
{
    /** @Max(10) **/
    public $id;
    /** @Max(100) **/
    public $stock;
}

class Product7 extends AbstractEntity
{
    /** @Max(value=10) **/
    public $id;
    /**
     * @CList({
     *    @Max(value=20,groups={"a"}),
     *    @Max(value=30,groups={"c"})
     * })
     */
    public $id2;
    /** @Max(value=100,groups={"Default","a"})  @Min(value=110,groups={"b"}) **/
    public $stock;
}

class Product8 extends AbstractEntity
{
    /** @Max(10) **/
    protected $id;
    /**
     * @CList({
     *    @Max(20),
     *    @Max(value=10,groups={"c"})
     * })
     */
    public $id2;
}

class Product9 extends AbstractEntity
{
    /** @Test1 **/
    protected $id;
    /**
     * @CList({
     *    @Max(20),
     *    @Test1(groups={"c"})
     * })
     */
    public $id2;
    /** @Test2 **/
    protected $id3;
}

/**
 * @GroupSequence({"a","b","c"})
 */
class Product10 extends AbstractEntity
{
    /** @Max(value=10,groups={"c"}) **/
    protected $id;
    /** @Max(value=10,groups={"b"}) **/
    protected $id2;
    /** @Max(value=10,groups={"a"}) **/
    protected $id3;
}

/**
 * @Annotation
 * @Target({ METHOD, FIELD, ANNOTATION_TYPE }) #, CONSTRUCTOR, PARAMETER
 * @Constraint(validatedBy = {})
 * @Max(10)
 */
class Test1 extends AbstractConstraint
{
    public $message = "must be false.";
    public $groups = array();
    public $payload = array();
}

class Test1Validator implements ConstraintValidator
{
    public function initialize(ConstraintInterface $constraint)
    {
    }

    public function isValid($value, ConstraintValidatorContext $context)
    {
        return true;
    }
}

/**
 * @Annotation
 * @Target({ METHOD, FIELD, ANNOTATION_TYPE }) #, CONSTRUCTOR, PARAMETER
 * @Constraint(validatedBy = {})
 * @Test1
 */
class Test2 extends AbstractConstraint
{
    public $message = "must be false.";
    public $groups = array();
    public $payload = array();
}

class Test2Validator implements ConstraintValidator
{
    public function initialize(ConstraintInterface $constraint)
    {
    }

    public function isValid($value, ConstraintValidatorContext $context)
    {
        return true;
    }
}


class ProductPOPO extends AbstractEntity
{
    protected $id;
    protected $id2;
    protected $stock;
}


class Test extends TestCase
{
    public function setUp()
    {
    }

    public function getConfigCacheFactory()
    {
        $config = array(
                //'fileCachePath'   => __DIR__.'/../cache',
                'configCache' => array(
                    'enableMemCache'  => true,
                    'enableFileCache' => true,
                    'forceFileCache'  => false,
                ),
                //'apcTimeOut'      => 20,
                'memCache' => array(
                    'class' => 'Rindow\Stdlib\Cache\SimpleCache\ArrayCache',
                ),
                'fileCache' => array(
                    'class' => 'Rindow\Stdlib\Cache\SimpleCache\ArrayCache',
                ),
        );
        $configCacheFactory = new ConfigCacheFactory($config);
        return $configCacheFactory;
    }

    public function encodeConsoleCode($text)
    {
        switch(PHP_OS) {
            case 'WIN32':
            case 'WINNT':
                $code = "SJIS";
                break;
             
            default:
                $code = "JIS";
                break;
         } 
        return mb_convert_encoding($text, $code, "auto");
    }

    public function testCombination()
    {
        $validator = new Validator(new AnnotationManager());

        $product = new Product4();

        $product->setId(10);
        $product->setStock(100);
        $violation = $validator->validate($product);
        $this->assertEquals(0,count($violation));

        $product->setId(11);
        $product->setStock(100);
        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 10.",$violation['id'][0]->getMessage());
        $this->assertEquals('id',$violation['id'][0]->getPropertyPath());
        $this->assertEquals(11,$violation['id'][0]->getInvalidValue());
        $this->assertEquals(__NAMESPACE__.'\Product4',get_class($violation['id'][0]->getRootBean()));

        $product->setId(null);
        $product->setStock(100);
        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals("may not be null.",$violation['id'][0]->getMessageTemplate());
        $this->assertEquals("may not be null.",$violation['id'][0]->getMessage());

        $product->setId(10);
        $product->setStock(101);
        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 100.",$violation['stock'][0]->getMessage());

        $product->setId(11);
        $product->setStock(101);
        $violation = $validator->validate($product);
        $this->assertEquals(2,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 10.",$violation['id'][0]->getMessage());
        $this->assertEquals("must be less than or equal to {value}.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 100.",$violation['stock'][0]->getMessage());

        $product->setId(null);
        $product->setStock(101);
        $violation = $validator->validate($product);
        $this->assertEquals(2,count($violation));
        $this->assertEquals("may not be null.",$violation['id'][0]->getMessageTemplate());
        $this->assertEquals("may not be null.",$violation['id'][0]->getMessage());
        $this->assertEquals("must be less than or equal to {value}.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 100.",$violation['stock'][0]->getMessage());

        $product->setId(10);
        $product->setStock(null);
        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals("may not be null.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("may not be null.",$violation['stock'][0]->getMessage());

        $product->setId(11);
        $product->setStock(null);
        $violation = $validator->validate($product);
        $this->assertEquals(2,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 10.",$violation['id'][0]->getMessage());
        $this->assertEquals("may not be null.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("may not be null.",$violation['stock'][0]->getMessage());

        $product->setId(null);
        $product->setStock(null);
        $violation = $validator->validate($product);
        $this->assertEquals(2,count($violation));
        $this->assertEquals("may not be null.",$violation['id'][0]->getMessageTemplate());
        $this->assertEquals("may not be null.",$violation['id'][0]->getMessage());
        $this->assertEquals("may not be null.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("may not be null.",$violation['stock'][0]->getMessage());
    }

    public function testValidateBean()
    {
        $validator = new Validator(new AnnotationManager());

        $product = new Product();
        $product->setId(11);
        $product->setId2(9);
        $product->setStock(101);

        $violation = $validator->validate($product);
        $this->assertEquals(3,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 10.",$violation['id'][0]->getMessage());
        $this->assertEquals("must be greater than or equal to {value}.",$violation['id2'][0]->getMessageTemplate());
        $this->assertEquals("must be greater than or equal to 10.",$violation['id2'][0]->getMessage());
        $this->assertEquals("must be less than or equal to {value}.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 100.",$violation['stock'][0]->getMessage());

        $product->setId(null);
        $product->setId2(null);
        $product->setStock(100);
        $violation = $validator->validate($product);
        $this->assertEquals(0,count($violation));

        $product->setId2(0);
        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals("must be greater than or equal to {value}.",$violation['id2'][0]->getMessageTemplate());
        $this->assertEquals("must be greater than or equal to 10.",$violation['id2'][0]->getMessage());
    }

    public function testMessage()
    {
        $validator = new Validator(new AnnotationManager());

        $product = new Product2();
        $product->setId(1);
        $product->setStock(101);

        $violation = $validator->validate($product);
        $this->assertEquals(2,count($violation));
        $this->assertEquals("must be greater than or equal to {value}.",$violation['id'][0]->getMessageTemplate());
        $this->assertEquals("must be greater than or equal to 10.",$violation['id'][0]->getMessage());
        $this->assertEquals("stock max is {value}.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("stock max is 100.",$violation['stock'][0]->getMessage());
    }

    public function testMultiParam()
    {
        $validator = new Validator(new AnnotationManager());

        $product = new Product3();
        $product->setId(11);
        $product->setName('abcdefghijk');
        $product->setDescription('abcd');
        $product->setCode('abcdefghijk');

        $violation = $validator->validate($product);
        $this->assertEquals(4,count($violation));
        $this->assertEquals("must be less than or equal to 10.",$violation['id'][0]->getMessage());
        $this->assertEquals("name max length is 10.",$violation['name'][0]->getMessage());
        $this->assertEquals("description min length is 5.",$violation['description'][0]->getMessage());
        $this->assertEquals("code length is 5 - 10.",$violation['code'][0]->getMessage());

        $product->setId(10);
        $product->setName('abcdefghij');
        $product->setDescription('abcde');
        $product->setCode('abcdefghij');
        $violation = $validator->validate($product);
        $this->assertEquals(0,count($violation));

        $product->setCode('abcd');
        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals("code length is 5 - 10.",$violation['code'][0]->getMessage());
    }

    public function testI18nTranslator()
    {
        $translator = new Translator();
        $translator->bindTextDomain(
            Validator::getTranslatorTextDomain(),
            Validator::getTranslatorBasePath()
        );
        $translator->setLocale('ja_JP');
        $translator->setTextDomain(Validator::getTranslatorTextDomain());

        $validator = new Validator(new AnnotationManager(), $translator);

        $product = new Product();
        $product->setId(11);
        $product->setStock(101);
        $violation = $validator->validate($product);
        $this->assertEquals(2,count($violation));
        //echo $this->encodeConsoleCode($violation['id']->getMessage());
        $this->assertEquals('10以下でなければなりません。',$violation['id'][0]->getMessage());

        $translator->setLocale('en_US');
        $validator = new Validator(new AnnotationManager(), $translator);

        $product = new Product();
        $product->setId(11);
        $product->setStock(101);
        $violation = $validator->validate($product);
        $this->assertEquals(2,count($violation));
        //echo $this->encodeConsoleCode($violation['id']->getMessage());
        $this->assertEquals('must be less than or equal to 10.',$violation['id'][0]->getMessage());

    }

    public function testCache()
    {
        $configCacheFactory = $this->getConfigCacheFactory();
        // miss cache
        $validator = new Validator(new AnnotationManager($configCacheFactory));

        $product = new Product();
        $product->setId(11);
        $product->setStock(101);

        $violation = $validator->validate($product);
        $this->assertEquals(2,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 10.",$violation['id'][0]->getMessage());
        $this->assertEquals("must be less than or equal to {value}.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 100.",$violation['stock'][0]->getMessage());

        $product->setId(10);
        $product->setStock(100);
        $violation = $validator->validate($product);
        $this->assertEquals(0,count($violation));

        // hit cache
        $validator = new Validator(new AnnotationManager($configCacheFactory));

        $product = new Product();
        $product->setId(11);
        $product->setStock(101);

        $violation = $validator->validate($product);
        $this->assertEquals(2,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 10.",$violation['id'][0]->getMessage());
        $this->assertEquals("must be less than or equal to {value}.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 100.",$violation['stock'][0]->getMessage());

        $product->setId(10);
        $product->setStock(100);
        $violation = $validator->validate($product);
        $this->assertEquals(0,count($violation));
    }

    public function testTargetMethod()
    {
        $validator = new Validator(new AnnotationManager());

        $product = new Product5();
        $product->setId(10);
        $product->setPassword('aaaaaaaa');
        $product->setPasswordAgain('bbbbbbbbb');

        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals("Passwords do not match.",$violation['password'][0]->getMessage());

        $product->setPasswordAgain('aaaaaaaa');
        $violation = $validator->validate($product);
        $this->assertEquals(0,count($violation));
    }

    public function testPropertyAccess()
    {
        $validator = new Validator(new AnnotationManager());

        $product = new Product6();
        $product->id = 11;
        $product->stock = 101;

        $violation = $validator->validate($product);
        $this->assertEquals(2,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 10.",$violation['id'][0]->getMessage());
        $this->assertEquals("must be less than or equal to {value}.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 100.",$violation['stock'][0]->getMessage());

        $product->id = 10;
        $product->stock = 100;
        $violation = $validator->validate($product);
        $this->assertEquals(0,count($violation));
    }

    public function testGroup()
    {
        $validator = new Validator(new AnnotationManager());

        $product = new Product7();
        $product->setId(11);
        $product->setId2(21);
        $product->setStock(101);

        $violation = $validator->validate($product);
        $this->assertEquals(2,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 10.",$violation['id'][0]->getMessage());
        $this->assertEquals("must be less than or equal to {value}.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 100.",$violation['stock'][0]->getMessage());

        $violation = $validator->validate($product,'a');
        $this->assertEquals(2,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id2'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 20.",$violation['id2'][0]->getMessage());
        $this->assertEquals("must be less than or equal to {value}.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 100.",$violation['stock'][0]->getMessage());

        $violation = $validator->validate($product,array('Default','a'));
        $this->assertEquals(3,count($violation));

        $violation = $validator->validate($product,array('c'));
        $this->assertEquals(0,count($violation));

        $product->setId2(31);
        $violation = $validator->validate($product,array('c'));
        $this->assertEquals(1,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id2'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 30.",$violation['id2'][0]->getMessage());
    }

    public function testGroupSequence()
    {
        $validator = new Validator(new AnnotationManager());

        $product = new Product7();
        $product->setId(11);
        $product->setId2(21);
        $product->setStock(101);

        $seq = new GroupSequence(array(
            'Default','a'
        ));
        $violation = $validator->validate($product,$seq);
        $this->assertEquals(2,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 10.",$violation['id'][0]->getMessage());
        $this->assertEquals("must be less than or equal to {value}.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 100.",$violation['stock'][0]->getMessage());
    }

    public function testGroupSequence2()
    {
        $validator = new Validator(new AnnotationManager());

        $product = new Product10();
        $product->setId(11);
        $product->setId2(11);
        $product->setId3(11);

        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals("must be less than or equal to 10.",$violation['id3'][0]->getMessage());

        $violation = $validator->validate($product,'Default');
        $this->assertEquals(1,count($violation));
        $this->assertEquals("must be less than or equal to 10.",$violation['id3'][0]->getMessage());

        $product->setId3(10);
        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals("must be less than or equal to 10.",$violation['id2'][0]->getMessage());

        $product->setId2(10);
        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals("must be less than or equal to 10.",$violation['id'][0]->getMessage());

        $product->setId(10);
        $violation = $validator->validate($product);
        $this->assertEquals(0,count($violation));
    }

    public function testValidateProperty()
    {
        $validator = new Validator(new AnnotationManager());

        $product = new Product7();
        $product->setId(11);
        $product->setId2(21);
        $product->setStock(101);

        $violation = $validator->validateProperty($product,'stock');
        $this->assertEquals(1,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 100.",$violation['stock'][0]->getMessage());

        $violation = $validator->validateProperty($product,'stock','b');
        $this->assertEquals(1,count($violation));
        $this->assertEquals("must be greater than or equal to {value}.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("must be greater than or equal to 110.",$violation['stock'][0]->getMessage());

        $product->setStock(100);
        $violation = $validator->validateProperty($product,'stock');
        $this->assertEquals(0,count($violation));

    }

    public function testValidateValue()
    {
        $validator = new Validator(new AnnotationManager());

        $className = __NAMESPACE__.'\Product7';

        $violation = $validator->validateValue($className,'stock',101);
        $this->assertEquals(1,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 100.",$violation['stock'][0]->getMessage());

        $violation = $validator->validateValue($className,'stock',101,'b');
        $this->assertEquals(1,count($violation));
        $this->assertEquals("must be greater than or equal to {value}.",$violation['stock'][0]->getMessageTemplate());
        $this->assertEquals("must be greater than or equal to 110.",$violation['stock'][0]->getMessage());

        $violation = $validator->validateValue($className,'stock',100);
        $this->assertEquals(0,count($violation));

    }

    public function testGetConstraints()
    {
        $validator = new Validator(new AnnotationManager());

        $className = __NAMESPACE__.'\Product7';
        $constraints = $validator->getConstraintsForClass($className);
        $this->assertEquals(3,count($constraints));
        //print_r($constraints);
        $this->assertEquals('Rindow\Validation\Constraints\Max',get_class($constraints['id'][0]->constraint));
        $this->assertEquals('Rindow\Validation\Constraints\Max',get_class($constraints['id2'][0]->constraint));
        $this->assertEquals('Rindow\Validation\Constraints\Max',get_class($constraints['stock'][0]->constraint));
        $this->assertEquals('Rindow\Validation\Constraints\Min',get_class($constraints['stock'][1]->constraint));
    }

    public function testNest()
    {
        $validator = new Validator(new AnnotationManager());

        $product = new Product8();
        $product->setId(11);
        $product->setId2(11);

        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 10.",$violation['id'][0]->getMessage());

        $violation = $validator->validate($product,'c');
        $this->assertEquals(1,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id2'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 10.",$violation['id2'][0]->getMessage());

        $product->setId2(21);
        $violation = $validator->validate($product);
        $this->assertEquals(2,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 10.",$violation['id'][0]->getMessage());
        $this->assertEquals("must be less than or equal to {value}.",$violation['id2'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 20.",$violation['id2'][0]->getMessage());
    }

    public function testNest2()
    {
        $validator = new Validator(new AnnotationManager());

        $product = new Product9();
        $product->setId(11);
        $product->setId2(11);

        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 10.",$violation['id'][0]->getMessage());

        $violation = $validator->validate($product,'c');
        $this->assertEquals(1,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id2'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 10.",$violation['id2'][0]->getMessage());

        $product->setId2(21);
        $violation = $validator->validate($product);
        $this->assertEquals(2,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 10.",$violation['id'][0]->getMessage());
        $this->assertEquals("must be less than or equal to {value}.",$violation['id2'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 20.",$violation['id2'][0]->getMessage());

        $product->setId(null);
        $product->setId2(null);
        $product->setId3(11);
        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals("must be less than or equal to {value}.",$violation['id3'][0]->getMessageTemplate());
        $this->assertEquals("must be less than or equal to 10.",$violation['id3'][0]->getMessage());
    }

    public function testOnModule()
    {
        $config = array(
            'module_manager' => array(
                'modules' => array(
                    'Rindow\Validation\Module' => true,
                    'Rindow\Stdlib\I18n\Module' => true,
                ),
                'annotation_manager' => true,
                'enableCache' => false,
            ),
        );
        $moduleManager = new ModuleManager($config);
        $sm = $moduleManager->getServiceLocator();
        $translator = $sm->get('Rindow\Stdlib\I18n\DefaultTranslator');
        $translator->setLocale('ja_JP');
        $validator = $sm->get('Rindow\Validation\DefaultValidator');

        $product = new Product();
        $product->setId(11);
        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals('10以下でなければなりません。',$violation['id'][0]->getMessage());
    }

    public function testOnModuleWithoutTranslator()
    {
        $config = array(
            'module_manager' => array(
                'modules' => array(
                    'Rindow\Validation\Module' => true,
                ),
                'annotation_manager' => true,
                'enableCache' => false,
            ),
        );
        $moduleManager = new ModuleManager($config);
        $sm = $moduleManager->getServiceLocator();
        $validator = $sm->get('Rindow\Validation\DefaultValidator');
        $this->assertNull($validator->getTranslator());

        $product = new Product();
        $product->setId(11);
        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals('must be less than or equal to 10.',$violation['id'][0]->getMessage());
    }

    public function testArrayConstraintContext()
    {
        $className = __NAMESPACE__.'\Product';
        $config = array(
            'builders' => array(
                'array' => array(
                    'mapping' => array(
                        $className => array(
                            'properties' => array(
                                'id' => array(
                                    'Max' => 20,
                                ),
                                'id2' => array(
                                    'Min' => 20,
                                ),
                                'stock' => array(
                                    'Max' => 200,
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
        $constraintManager = new ConstraintContextManager(null,null,new AnnotationManager());
        $constraintManager->setConfig($config);
        $validator = new Validator(null,null,$constraintManager);
        $product = new $className();
        $product->setId(21);
        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals('must be less than or equal to 20.',$violation['id'][0]->getMessage());
    }

    public function testArrayConstraintContextOnModuleAndDisableAnnotation()
    {
        $className = __NAMESPACE__.'\Product';
        $config = array(
            'module_manager' => array(
                'modules' => array(
                    'Rindow\Validation\Module' => true,
                    'Rindow\Stdlib\I18n\Module' => true,
                ),
                'annotation_manager' => true,
                'enableCache' => false,
            ),
            'validator' => array(
                'builders' => array(
                    'annotation' => false,
                    'array' => array(
                        'mapping' => array(
                            $className => array(
                                'properties' => array(
                                    'id' => array(
                                        'Max' => 20,
                                    ),
                                    'id2' => array(
                                        'Min' => 20,
                                    ),
                                    'stock' => array(
                                        'Max' => 200,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
        $moduleManager = new ModuleManager($config);
        $sm = $moduleManager->getServiceLocator();
        $validator = $sm->get('Rindow\Validation\DefaultValidator');
        $product = new $className();
        $product->setId(21);
        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals('must be less than or equal to 20.',$violation['id'][0]->getMessage());
    }

    public function testArrayConstraintContextOnModulePOPOAndEnableAnnotation()
    {
        $className = __NAMESPACE__.'\ProductPOPO';
        $config = array(
            'module_manager' => array(
                'modules' => array(
                    'Rindow\Validation\Module' => true,
                    'Rindow\Stdlib\I18n\Module' => true,
                ),
                'annotation_manager' => true,
                'enableCache' => false,
            ),
            'validator' => array(
                'builders' => array(
                    'array' => array(
                        'mapping' => array(
                            $className => array(
                                'properties' => array(
                                    'id' => array(
                                        'Max' => 20,
                                    ),
                                    'id2' => array(
                                        'Min' => 20,
                                    ),
                                    'stock' => array(
                                        'Max' => 200,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
        $moduleManager = new ModuleManager($config);
        $sm = $moduleManager->getServiceLocator();
        $validator = $sm->get('Rindow\Validation\DefaultValidator');
        $product = new $className();
        $product->setId(21);
        $violation = $validator->validate($product);
        $this->assertEquals(1,count($violation));
        $this->assertEquals('must be less than or equal to 20.',$violation['id'][0]->getMessage());
    }


    public function testNoneConstraints()
    {
        $className = __NAMESPACE__.'\ProductPOPO';
        $config = array(
            'module_manager' => array(
                'modules' => array(
                    'Rindow\Validation\Module' => true,
                    'Rindow\Stdlib\I18n\Module' => true,
                ),
                'annotation_manager' => true,
                'enableCache' => false,
            ),
        );
        $moduleManager = new ModuleManager($config);
        $sm = $moduleManager->getServiceLocator();
        $validator = $sm->get('Rindow\Validation\DefaultValidator');
        $product = new $className();
        $product->setId(21);
        $violation = $validator->validate($product);
        $this->assertEquals(0,count($violation));
    }
}
