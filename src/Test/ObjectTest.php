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
     * create object and check data returned by get* methods
     * 
     * @param int $first
     * @param int $second
     * 
     * @dataProvider baseDataProvider
     * @requires baseDataProvider
     */
    public function testCreateSimpleObject($first, $second)
    {
        $a = new Object([
            'data_first'    => $first,
            'data_second'   => $second,
        ]);

        $this->assertEquals($first, $a->getDataFirst());
        $this->assertEquals($second, $a->getDataSecond());
    }

    /**
     * return data for base example
     * 
     * @return array
     */
    public function baseDataProvider()
    {
        return [[1, 2]];
    }
}
