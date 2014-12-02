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

use ClassKernel\Data\Object;

class ObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * check set current data as original data
     *
     * @requires _getSimpleData
     */
    public function testDataValidation()
    {
        $object = new Object();
        $object->putValidationRule('#data_first#', '#^[\d]+$#');
        $object->putValidationRule('#data_second#', '#[\w]*#');
        $object->putValidationRule('#data_(third|fourth)#', function ($key, $data) {
            if (is_null($data)) {
                return true;
            }
            return false;
        });

        $object->setData([
            'data_first'    => 'first data',
            'data_second'   => 'second data',
            'data_third'    => 'third data',
            'data_fourth'   => null,
        ]);

        $this->assertTrue($object->checkErrors());
        $this->assertEquals($object->returnObjectError()[0], [
            "message" => "validation_mismatch",
            "key"=> "data_first",
            "data"=> "first data",
            "rule"=> "#^[\\d]+$#"
        ]);
        $this->assertEquals($object->returnObjectError()[1], [
            "message"=> "validation_mismatch",
            "key"=> "data_third",
            "data"=> "third data",
            "rule"=>  'Closure [ <user> public method Test\{closure} ] {
  @@ /home/zmp/ftp/CLASS/class-kernel/src/Test/ObjectTest.php 26 - 31

  - Parameters [2] {
    Parameter #0 [ <required> $key ]
    Parameter #1 [ <required> $data ]
  }
}
']);
        $this->assertCount(2, $object->returnObjectError());
    }

    /**
     * check that object after creation has some errors
     *
     * @param int $first
     * @param int $second
     *
     * @dataProvider baseDataProvider
     * @requires baseDataProvider
     * @requires _simpleObject
     */
    public function testCreateSimpleObject($first, $second)
    {
        $object = $this->_simpleObject($first, $second);

        $this->assertFalse($object->checkErrors());
        $this->assertEmpty($object->returnObjectError());
    }

    /**
     * check data returned by get* methods
     * 
     * @param int $first
     * @param int $second
     * 
     * @dataProvider baseDataProvider
     * @requires baseDataProvider
     * @requires _simpleObject
     */
    public function testGetDataFromObject($first, $second)
    {
        $object = $this->_simpleObject($first, $second);

        $this->assertEquals($first, $object->getDataFirst());
        $this->assertEquals($second, $object->toArray('data_second'));
        $this->assertEquals($second, $object['data_second']);
        $this->assertNull($object->getDataNotExists());

        $this->assertEquals(
            $this->_getSimpleData($first, $second),
            $object->toArray()
        );
    }

    /**
     * check data with has*, is* and not* magic methods
     *
     * @param int $first
     * @param int $second
     *
     * @dataProvider baseDataProvider
     * @requires baseDataProvider
     * @requires _simpleObject
     */
    public function testCheckingData($first, $second)
    {
        $object = $this->_simpleObject($first, $second);

        $this->assertTrue($object->hasDataFirst());
        $this->assertFalse($object->hasDataNotExists());

        $this->assertTrue(isset($object['data_first']));
        $this->assertFalse(isset($object['data_not_exist']));

        $this->assertTrue($object->isDataFirst($first));
        $this->assertFalse($object->isDataFirst('1'));

        $this->assertTrue($object->notDataFirst('1'));
        $this->assertFalse($object->notDataFirst($first));
    }

    /**
     * check add data by set* magic method with information about value exist and object changes
     *
     * @param int $first
     * @param int $second
     *
     * @dataProvider baseDataProvider
     * @requires baseDataProvider
     * @requires _simpleObject
     */
    public function testSetDataInObjectByMagicMethods($first, $second)
    {
        $object = $this->_simpleObject($first, $second);

        $this->assertFalse($object->hasDataThird());
        $this->assertFalse($object->dataChanged());

        $object->setDataThird(3);
        $object['data_fourth'] = 4;

        $this->assertTrue($object->hasDataThird());
        $this->assertTrue($object->hasData('data_fourth'));
        $this->assertTrue($object->dataChanged());

        $this->assertFalse($object->checkErrors());
    }

    /**
     * check add data by setData method with information about value exist and object changes
     *
     * @param int $first
     * @param int $second
     *
     * @dataProvider baseDataProvider
     * @requires baseDataProvider
     * @requires _simpleObject
     */
    public function testSetDataInObjectByDataMethod($first, $second)
    {
        $object = $this->_simpleObject($first, $second);

        $this->assertFalse($object->hasDataThird());
        $this->assertFalse($object->hasData('data_fourth'));
        $this->assertFalse($object->dataChanged());

        $object->setData([
            'data_third'    => 3,
            'data_fourth'   => 4,
        ]);

        $this->assertTrue($object->hasDataThird());
        $this->assertTrue($object->hasData('data_fourth'));
        $this->assertTrue($object->dataChanged());

        $this->assertFalse($object->checkErrors());
    }

    /**
     * check removing and clearing data with information about value exist and object changes
     *
     * @param int $first
     * @param int $second
     *
     * @dataProvider baseDataProvider
     * @requires baseDataProvider
     * @requires _simpleObject
     */
    public function testRemovingData($first, $second)
    {
        $object = $this->_simpleObject($first, $second);

        $this->assertFalse($object->dataChanged());

        $object->clearDataFirst();
        $this->assertNull($object->getDataFirst());
        $this->assertTrue($object->hasDataFirst());

        unset($object['data_first']);
        $this->assertFalse($object->hasDataFirst());

        $object->unsetDataSecond();
        $this->assertNull($object->getDataSecond());
        $this->assertFalse($object->hasDataSecond());

        $this->assertTrue($object->dataChanged());
    }

    /**
     * check that access to non existing method will create error information
     */
    public function testAccessForNonExistingMethods()
    {
        $object = new Object();
        $object->executeNonExistingMethod();

        $this->assertTrue($object->checkErrors());
        $this->assertArrayHasKey('wrong_method', $object->returnObjectError());
    }

    /**
     * check restore data for single key
     *
     * @param int $first
     * @param int $second
     *
     * @dataProvider baseDataProvider
     * @requires baseDataProvider
     * @requires _simpleObject
     */
    public function testDataRestorationForSingleData($first, $second)
    {
        $object = $this->_simpleObject($first, $second);

        $this->assertFalse($object->dataChanged());
        $this->assertFalse($object->keyDataChanged('data_first'));
        $object->setDataFirst('bar');

        $this->assertTrue($object->dataChanged());
        $this->assertEquals('bar', $object->getDataFirst());
        $this->assertEquals($first, $object->returnOriginalData('data_first'));
        $this->assertTrue($object->keyDataChanged('data_first'));

        $object->restoreDataFirst();
        $this->assertEquals($first, $object->getDataFirst());
        $this->assertTrue($object->dataChanged());
    }

    /**
     * check restoration for all data in object with change dataChanged value
     *
     * @param int $first
     * @param int $second
     *
     * @dataProvider baseDataProvider
     * @requires baseDataProvider
     * @requires _simpleObject
     */
    public function testFullDataRestoration($first, $second)
    {
        $object = $this->_simpleObject($first, $second);

        $this->assertFalse($object->dataChanged());
        $object->setDataFirst('bar');
        $object->setDataSecond('moo');
        $this->assertTrue($object->dataChanged());

        $object->restoreData();
        $this->assertEquals(
            $this->_getSimpleData($first, $second),
            $object->toArray()
        );
        $this->assertFalse($object->dataChanged());
    }

    /**
     * check set current data as original data
     *
     * @param int $first
     * @param int $second
     *
     * @dataProvider baseDataProvider
     * @requires baseDataProvider
     * @requires _simpleObject
     */
    public function testDataReplacement($first, $second)
    {
        $object = $this->_simpleObject($first, $second);

        $this->assertFalse($object->dataChanged());
        $object->setDataFirst('bar');
        $object->setDataSecond('moo');
        $this->assertTrue($object->dataChanged());

        $object->replaceDataArrays();

        $this->assertFalse($object->dataChanged());
    }

    /**
     * check usage object as array (access data and loop processing)
     *
     * @param int $first
     * @param int $second
     *
     * @dataProvider baseDataProvider
     * @requires baseDataProvider
     * @requires _simpleObject
     */
    public function testAccessToDataAsArray($first, $second)
    {
        $object = $this->_simpleObject($first, $second);

        foreach ($object as $key => $val) {
            if ($key === 'data_first') {
                $this->assertEquals($first, $val);
            }
            if ($key === 'data_second') {
                $this->assertEquals($second, $val);
            }
        }

        $this->assertEquals($object['data_first'], $first);
    }

    /**
     * check access and setup data by object attributes
     *
     * @param int $first
     * @param int $second
     *
     * @dataProvider baseDataProvider
     * @requires baseDataProvider
     * @requires _simpleObject
     */
    public function testAccessToDataByAttributes($first, $second)
    {
        $object = $this->_simpleObject($first, $second);

        $this->assertEquals($object->data_first, $first);
        $this->assertNull($object->data_non_exists);

        $object->data_third = 'data third';
        $this->assertEquals($object->data_third, 'data third');
    }

    /**
     * check echoing of object
     * with separator changing
     *
     * @requires _simpleObject
     */
    public function testDisplayObjectAsString()
    {
        $object = $this->_simpleObject('first data', 'second data');
        $this->assertEquals('first data, second data', (string)$object);

        $object->changeSeparator('; ');
        $this->assertEquals('first data; second data', (string)$object);
    }

    /**
     * allow to change data before insert for founded key using closure
     *
     * @param int $first
     * @param int $second
     *
     * @dataProvider baseDataProvider
     * @requires baseDataProvider
     * @requires _simpleObject
     */
    public function testDataPreparationOnEnter($first, $second)
    {
        $object = new Object();
        $object->putPreparationCallback('#data_[\w]+#', function ($key, $value) {
            if ($key === 'data_second') {
                return 'second';
            }

            $value .= '_modified';
            return $value;
        });

        $object->setDataFirst($first);
        $object->setDataSecond($second);

        $this->assertEquals($first . '_modified', $object->getDataFirst());
        $this->assertEquals('second', $object->toArray('data_second'));

        $object->setData([
            'data_third'    => 'bar',
            'data_fourth'   => 'moo',
        ]);
    }

    /**
     * allow to change data before return for founded key using closure
     *
     * @param int $first
     * @param int $second
     *
     * @dataProvider baseDataProvider
     * @requires baseDataProvider
     * @requires _simpleObject
     */
    public function testDataPreparationOnReturn($first, $second)
    {
        $object = new Object();
        $object->putReturnCallback('#data_[\w]+#', function ($key, $value) {
            if ($key === 'data_second') {
                return 'second';
            }

            $value .= '_modified';
            return $value;
        });

        $object->setDataFirst($first);
        $object->setDataSecond($second);

        $this->assertEquals($first . '_modified', $object->getDataFirst());
        $this->assertEquals('second', $object->toArray('data_second'));
    }

    /**
     * allow to create object with given json string
     *
     * @param int $first
     * @param int $second
     *
     * @dataProvider baseDataProvider
     * @requires baseDataProvider
     * @requires _simpleObject
     * @requires _exampleJsonData
     */
    public function testCreationWithJsonData($first, $second)
    {
        $jsonData = $this->_exampleJsonData($first, $second);

        $object = new Object([
            'data'  => $jsonData,
            'type'  => 'json',
        ]);

        $this->assertEquals($first, $object->getDataFirst());
        $this->assertEquals($second, $object->toArray('data_second'));
    }

    /**
     * allow to create object with given stdClass object
     *
     * @param int $first
     * @param int $second
     *
     * @dataProvider baseDataProvider
     * @requires baseDataProvider
     * @requires _simpleObject
     * @requires _exampleStdData
     */
    public function testCreationWithStdClassData($first, $second)
    {
        $std = $this->_exampleStdData($first, $second);

        $object = new Object($std);

        $this->assertEquals($first, $object->getDataFirst());
        $this->assertEquals($second, $object->toArray('data_second'));
    }

    /**
     * allow to create object with given json string
     *
     * @param int $first
     * @param int $second
     *
     * @dataProvider baseDataProvider
     * @requires baseDataProvider
     * @requires _simpleObject
     * @requires _exampleJsonData
     */
//    public function testCreationWithJsonDataWithDataPreparation($first, $second)
//    {
//        $jsonData = $this->_exampleJsonData($first, $second);
//
//        $object             = new Object;
//        $dataPreparation    = [
//            '^data_first$' => function ($key, $val) {
//                return 'im changed';
//            }
//        ];
//
//        $object->putPreparationCallback($dataPreparation);
//        $object->appendJson($jsonData);
//    }

    /**
     * return data for base example
     * 
     * @return array
     */
    public function baseDataProvider()
    {
        return [
            [1, 2],
            ['first', 'second'],
            [true, false],
            [null, ['foo', 'bar']],
        ];
    }

    /**
     * create simple object to test
     * 
     * @param mixed $first
     * @param mixed $second
     * @return \ClassKernel\Data\Object
     */
    protected function _simpleObject($first, $second)
    {
        return new Object($this->_getSimpleData($first, $second));
    }

    /**
     * return basic data to test
     * 
     * @param mixed $first
     * @param mixed $second
     * @return array
     */
    protected function _getSimpleData($first, $second)
    {
        return [
            'data_first'    => $first,
            'data_second'   => $second,
        ];
    }

    /**
     * create simple xml data to test
     *
     * @param mixed $first
     * @param mixed $second
     * @return string
     */
    protected function _exampleSimpleXmlData($first, $second)
    {
        
    }

    /**
     * create xml data to test
     *
     * @param mixed $first
     * @param mixed $second
     * @return string
     */
    protected function _exampleXmlData($first, $second)
    {
        
    }

    /**
     * create json data to test
     * 
     * @param mixed $first
     * @param mixed $second
     * @return string
     */
    protected function _exampleJsonData($first, $second)
    {
        return json_encode($this->_getSimpleData($first, $second));
    }

    /**
     * create serialized string to test
     *
     * @param mixed $first
     * @param mixed $second
     * @return string
     */
    protected function _exampleSerializedData($first, $second)
    {
        
    }

    /**
     * create std object to test
     *
     * @param mixed $first
     * @param mixed $second
     * @return \stdClass
     */
    protected function _exampleStdData($first, $second)
    {
        $std                = new \stdClass;
        $std->data_first    = $first;
        $std->data_second   = $second;

        return $std;
    }
}
