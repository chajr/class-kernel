<?php
/**
 * test Xml
 *
 * @package     ClassKernel
 * @subpackage  Test
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree
 */
namespace Test;

use ClassKernel\Data\Xml;

class XmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test creating new xml object
     */
    public function testXmlCreation()
    {
        $xml = new Xml();
        $this->assertEquals('1.0', $xml->version);
        $this->assertEquals('UTF-8', $xml->encoding);

        $xml = new Xml([
            'version'   => '1.0',
            'encoding'  =>'iso-8859-1'
        ]);
        $this->assertEquals('1.0', $xml->version);
        $this->assertEquals('iso-8859-1', $xml->encoding);
    }

    /**
     * test search nodes by attribute name
     */
    public function testSearchByAttributeAccess()
    {
        $xml    = new Xml();
        $root   = $xml->createElement('root');

        $testNode = $xml->createElement('test');
        $testNode->setAttribute('attr', 'a');
        $root->appendChild($testNode);

        $testNode = $xml->createElement('test');
        $testNode->setAttribute('attr', 'b');
        $root->appendChild($testNode);

        $testNode = $xml->createElement('test');
        $testNode->setAttribute('attr', 'c');
        $root->appendChild($testNode);

        $xml->appendChild($root);
        $list = $xml->searchByAttribute($xml->childNodes, 'attr');

        $this->assertArrayHasKey('a', $list);
        $this->assertArrayHasKey('b', $list);
        $this->assertArrayHasKey('c', $list);
    }

    /**
     * test loading xml data from file
     */
    public function testFileLoading()
    {
        $testFile = 'examples/xml/source.xml';
        $this->assertFileExists($testFile, 'test file don\'t exists');

        $xml = new Xml;
        $xml->loadXmlFile($testFile);

        $this->assertFalse($xml->hasErrors());

        $root = $xml->documentElement;
        $this->assertEquals(
            'lorem ipsum',
            $root->getElementsByTagName('sub')->item(0)->nodeValue
        );
    }
}
