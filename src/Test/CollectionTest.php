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
     * @param Collection $collection
     * @param array $data
     * @dataProvider exampleCollectionObject
     * @requires exampleCollection
     * @requires _exampleCollectionObject
     */
    public function testCreateCollection($collection, array $data)
    {
        $this->assertEquals('lorem ipsum', $collection->first());
        $this->assertEquals($data[1]['data_first'], $collection->getElement(1)['data_first']);

        $collection = new Collection;
        $collection->appendArray($data);

        $this->assertEquals('lorem ipsum', $collection->first());
        $this->assertEquals($data[1]['data_first'], $collection->getElement(1)['data_first']);
    }

    /**
     * check usage collection as array (access data and loop processing)
     *
     * @param Collection $collection
     * @param array $data
     * @dataProvider exampleCollectionObject
     * @requires exampleCollection
     * @requires _exampleCollectionObject
     */
    public function testArrayAccessForCollection($collection, array $data)
    {
        foreach ($collection as $index => $element) {
            $this->assertEquals($data[$index], $element);
        }
    }

    /**
     * test some basic access to single collection elements
     *
     * @param Collection $collection
     * @param array $data
     * @dataProvider exampleCollectionObject
     * @requires exampleCollection
     * @requires _exampleCollectionObject
     */
    public function testBasicAccessToCollectionElements($collection, array $data)
    {
        $this->assertEquals('lorem ipsum', $collection->first());
        $this->assertEquals('lorem ipsum', $collection[0]);
        $this->assertEquals(1, $collection->last()['data_first']);
        $this->assertEquals($data[1]['data_first'], $collection->getElement(1)['data_first']);
        $this->assertEquals(9, $collection->count());
        $this->assertTrue($collection->hasElement(5));
    }

    /**
     * test correct set page size and count available pages
     *
     * @param Collection $collection
     * @dataProvider exampleCollectionObject
     * @requires exampleCollection
     * @requires _exampleCollectionObject
     */
    public function testPageInformation($collection)
    {
        $collection->setPageSize(2);
        $this->assertEquals(2, $collection->getPageSize());
        $this->assertEquals(5, $collection->countPages());
    }

    /**
     * test access to collection using pages
     *
     * @param Collection $collection
     * @param array $data
     * @dataProvider exampleCollectionObject
     * @requires exampleCollection
     * @requires _exampleCollectionObject
     */
    public function testPageAccessForCollection($collection, array $data)
    {
        $collection->setPageSize(2);
        $this->assertEquals(2, count($collection->getFirstPage()));
        $this->assertEquals(1, count($collection->getLastPage()));
        $this->assertEquals([$data[0], $data[1]], $collection->getFirstPage());
        $this->assertEquals([$data[8]], $collection->getLastPage());
        $this->assertEquals([$data[2], $data[3]], $collection->getPage(2));
        $this->assertNull($collection->getPage(10));
        $this->assertEquals(1, $collection->getCurrentPage());

        $collection->nextPage();

        $this->assertEquals(2, $collection->getCurrentPage());
        $this->assertEquals([$data[4], $data[5]], $collection->getNextPage());
        $this->assertEquals([$data[0], $data[1]], $collection->getPreviousPage());
    }

    public function testArrayAccessToCollectionPages()
    {
        
    }

    public function testCreateCollectionWithValidation()
    {
        
    }

    public function testCreateCollectionWithDataPreparation()
    {
        
    }

    public function testRemovingElementFromCollection()
    {
        
    }

    public function testReturnCollectionWithDataPreparation()
    {
        
    }

    public function testAddDataWithOriginalDataCheck()
    {
        
    }

    //add create collection with other than array data types

    /**
     * create collection object for test
     * 
     * @return Collection
     */
    public function exampleCollectionObject()
    {
        $data = $this->_exampleCollection();
        return [[
            new Collection([
                'data'  => $data
            ]),
            $data
        ]];
    }

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
