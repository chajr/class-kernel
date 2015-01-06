<?php
/**
 * test BlueObject using Object class
 *
 * @package     ClassKernel
 * @subpackage  Test
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree
 */
namespace Test;

use ClassKernel\Base\Register;

class RegisterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test conversion namespaces to class code
     */
    public function testNameConversion()
    {
        $namespace = 'Some\Module\Namespace';
        $converted = Register::name2code($namespace);

        $this->assertEquals('some_module_namespace', $converted);
    }

    /**
     * create object by register and check that object was created
     */
    public function testAddObjectToRegister()
    {
        $objectName = 'ClassKernel\Data\Object';
        /** @var \ClassKernel\Data\Object $object */
        $object     = Register::getObject($objectName);
        $converted  = Register::name2code($objectName);

        $this->assertEquals($objectName, get_class($object));
        $this->assertArrayHasKey($converted, Register::getClassCounter());
        $this->assertEquals(1, Register::getClassCounter()[$converted]);
        $this->assertFalse(Register::hasErrors());
    }

    /**
     * try to create don't existing object
     */
    public function testAddNotExistingObjectToMethod()
    {
        $objectName = 'ClassKernel\Data\NotExistingObject';
        $object     = Register::getObject($objectName);

        $this->assertFalse($object);
        $this->assertTrue(Register::hasErrors());
        $this->assertEquals($objectName . ' don\'t exist', Register::getErrors()[0]);

        Register::clearErrors();

        $this->assertFalse(Register::hasErrors());
    }

    /**
     * create object and give unlimited number of arguments
     */
    public function testAddObjectWithArguments()
    {
        $objectName = 'ClassKernel\Data\Object';
        /** @var \ClassKernel\Data\Object $object */
        $object = Register::getObject(
            $objectName,
            [
                [
                    'data' => ['first' => 1, 'second' => 2]
                ]
            ]
        );

        $this->assertEquals(1, $object->getFirst());
        $this->assertEquals(2, $object->getSecond());
    }

    /**
     * test creating class reflection instance
     */
    public function testGetObjectReflection()
    {
        $objectName = 'ClassKernel\Data\Object';
        /** @var \ReflectionClass $object */
        $object = Register::getObject($objectName, [], true);

        $this->assertEquals('ReflectionClass', get_class($object));
        $this->assertEquals($objectName, $object->getName());
    }

    /**
     * test create singleton object,  check that objects has the same data
     */
    public function testCreateSingletonObject()
    {
        $objectName = 'ClassKernel\Data\Object';
        /** @var \ClassKernel\Data\Object $objectOne */
        $objectOne = Register::getSingleton($objectName);
        $converted = Register::name2code($objectName);

        $objectOne->setFirst(1);

        /** @var \ClassKernel\Data\Object $objectOne */
        $objectTwo = Register::getSingleton($objectName);

        $this->assertEquals(1, $objectTwo->getFirst());
        $this->assertArrayHasKey($converted, Register::getRegisteredObjects());
        $this->assertEquals(3, Register::getClassCounter()[$converted]);
    }

    /**
     * check destroy singleton class instance and information about destroyed singleton
     */
    public function testDestroySingletonObject()
    {
        $objectName = 'ClassKernel\Data\Object';
        $converted  = Register::name2code($objectName);
        $this->assertArrayHasKey($converted, Register::getRegisteredObjects());

        Register::destroy($objectName);

        $this->assertArrayNotHasKey($converted, Register::getRegisteredObjects());
        $this->assertArrayHasKey($objectName, Register::getClassCounter());
        $this->assertEquals('destroyed [' . $objectName . ']', Register::getClassCounter()[$objectName]);
    }

    /**
     * test creating singleton with instance name
     */
    public function testCreateSingletonWithInstanceName()
    {
        $objectName = 'ClassKernel\Data\Object';
        $testName   = 'test_singleton';
        /** @var \ClassKernel\Data\Object $objectOne */
        $objectOne = Register::getSingleton($objectName, [], $testName);
        $converted = Register::name2code($objectName);

        $objectOne->setFirst(1);

        /** @var \ClassKernel\Data\Object $objectOne */
        $objectTwo = Register::getSingleton($testName);

        $this->assertEquals(1, $objectTwo->getFirst());
        $this->assertArrayHasKey($testName, Register::getRegisteredObjects());
        $this->assertEquals(4, Register::getClassCounter()[$converted]);
    }
}
