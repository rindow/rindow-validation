<?php
namespace RindowTest\Validation\ConstraintContextManagerTest;

use PHPUnit\Framework\TestCase;
use Rindow\Stdlib\Entity\AbstractEntity;
use Rindow\Validation\Core\ConstraintContextManager;
use Rindow\Validation\Constraints\Max;
use Rindow\Annotation\AnnotationManager;

class Normal extends AbstractEntity
{
    /** @Max(10) **/
    protected $id;
}

class Test extends TestCase
{
    public function setUp()
    {
    }

    public function testNormal()
    {
    	$className = __NAMESPACE__.'\Normal';
    	$manager = new ConstraintContextManager(null,null,new AnnotationManager());
    	$constraintContext = $manager->getConstraints($className);
    	$this->assertEquals(
    		'Rindow\Validation\Constraints\Max',
    		get_class($constraintContext['id'][0]->constraint)
    	);
    	$builders = $manager->getBuilders();
    	$this->assertEquals(
    		'Rindow\Validation\Builder\AnnotationConstraintContextBuilder',
    		get_class($builders[0]));
	}

    public function testConfig()
    {
    	$className = __NAMESPACE__.'\Normal';
    	$config = array(
    		'builders' => array(
    			'annotation' => array(),
    		),
    	);
        $manager = new ConstraintContextManager(null,null,new AnnotationManager());
    	$manager->setConfig($config);
    	$constraintContext = $manager->getConstraints($className);
    	$this->assertEquals(
    		'Rindow\Validation\Constraints\Max',
    		get_class($constraintContext['id'][0]->constraint)
    	);
    	$builders = $manager->getBuilders();
    	$this->assertEquals(
    		'Rindow\Validation\Builder\AnnotationConstraintContextBuilder',
    		get_class($builders[0]));
	}
}