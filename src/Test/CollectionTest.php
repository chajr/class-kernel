<?php
/**
 * test Collection Object class
 *
 * @package     ClassKernel
 * @subpackage  Test
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree
 */
namespace Test;

use ClassKernel\Data\Collection;
use ClassKernel\Data\Object;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test basic object creation
     * 
     * @requires exampleCollection
     */
    public function testCreateCollection()
    {
        $data = $this->_exampleCollection();
        $collection = new Collection([
            'data'  => $data
        ]);

        $this->assertEquals('lorem ipsum', $collection->first());
        $this->assertEquals($data[1]['data_first'], $collection->getElement(1)['data_first']);

        $collection = new Collection;
        $collection->appendArray($data);

        $this->assertEquals('lorem ipsum', $collection->first());
        $this->assertEquals($data[1]['data_first'], $collection->getElement(1)['data_first']);
    }

    public function testCreateCollectionWithValidation()
    {
        
    }

    public function testCreateCollectionWithDataPreparation()
    {
        
    }

    public function testArrayAccessForCollection()
    {
        
    }

    public function testRemovingElementFromCollection()
    {
        
    }

    /**
     * test some basic access to single collection elements
     * 
     * @requires exampleCollection
     */
    public function testBasicAccessToCollectionElements()
    {
        $data = $this->_exampleCollection();
        $collection = new Collection([
            'data'  => $data
        ]);

        $this->assertEquals('lorem ipsum', $collection->first());
        $this->assertEquals('lorem ipsum', $collection[0]);
        $this->assertEquals(1, $collection->last()['data_first']);
        $this->assertEquals($data[1]['data_first'], $collection->getElement(1)['data_first']);
        $this->assertEquals(9, $collection->count());
        $this->assertTrue($collection->hasElement(5));
    }

    public function testReturnCollectionWithDataPreparation()
    {
        
    }

    public function testPageAccessForCollection()
    {
        
    }

    public function testAddDataWithOriginalDataCheck()
    {
        
    }

    //add create collection with other than array data types

    /**
     * return some data to test collection functionality
     * 
     * @return array
     */
    protected function _exampleCollection()
    {
        $object = new Object(
            [
                'data' => [
                    'data_first'    => 'first',
                    'data_second'   => 2,
                    'data_third'    => false,
                ]
            ]
        );
        $object2 = clone $object;
        $object2->destroy();
        $object2->set([
            'data_first'    => 1,
            'data_second'   => 'second',
            'data_third'    => true,
        ]);

        return [
            'lorem ipsum',
            [
                'data_first'    => 1,
                'data_second'   => 2,
                'data_third'    => 3,
            ],
            [
                'data_first'    => true,
                'data_second'   => false,
                'data_third'    => null,
            ],
            [
                'data_first'    => 'first',
                'data_second'   => 'second',
                'data_third'    => 'third',
            ],
            [
                'data_first'    => '001',
                'data_second'   => '002',
                'data_third'    => '003',
            ],
            [
                'data_first'    => [1, 2, 3],
                'data_second'   => [4, 5, 6],
                'data_third'    => [7, 8, 9],
            ],
            [
                'data_first'    => 4,
                'data_second'   => 5,
                'data_third'    => 6,
            ],
            $object,
            $object2
        ];
    }
}
