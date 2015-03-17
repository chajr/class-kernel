<h5>By set methods</h5>
<code>
<pre>$object = new ClassKernel\Data\Object();
$object->setFirstData(1);
$object->setSecondData(2);
$object->set('third_data', 3);
$object->set([
    'fourth_data' => 4,
    'fifth_data'  => 5
]);</pre>
</code>
<?php
$object = new ClassKernel\Data\Object();
$object->setFirstData(1);
$object->setSecondData(2);
$object->set('third_data', 3);
$object->set([
    'fourth_data' => 4,
    'fifth_data'  => 5
]);
?>

<h5>By constructor</h5>
<code>
<pre>$objectArray = new ClassKernel\Data\Object([
    'data' => [
        'first_data'    => 'a',
        'second_data'   => 'b',
        'third_data'    => 'c',
    ]
]);

$json = '{first_data: "a", second_data: "b", third_data: "c"}';
$objectJson = new ClassKernel\Data\Object([
    'data' => $json,
    'type' => 'json'
]);

$std                = new stdClass();
$std->first_data    = 'a';
$std->second_data   = 'b';
$std->third_data    = 'c';
$serialized         = serialize($std);
$objectSerialized   = new ClassKernel\Data\Object([
    'data' => $serialized,
    'type' => 'serialized'
]);

$std = new stdClass();
$std->first_data    = 'a';
$std->second_data   = 'b';
$std->third_data    = 'c';
$objectStd   = new ClassKernel\Data\Object(['data' => $std]);</pre>
</code>
<?php
$objectArray = new ClassKernel\Data\Object([
    'data' => [
        'first_data'    => 'a',
        'second_data'   => 'b',
        'third_data'    => 'c',
    ]
]);

$json       = '{"first_data":"a","second_data":"b","third_data":"c"}';
$objectJson = new ClassKernel\Data\Object([
    'data' => $json,
    'type' => 'json'
]);

$serialized = serialize(
    'a:3:{s:10:"first_data";s:1:"a";s:11:"second_data";s:1:"b";s:10:"third_data";s:1:"c";}'
);
$objectSerialized   = new ClassKernel\Data\Object([
    'data' => $serialized,
    'type' => 'serialized'
]);

$std = new stdClass();
$std->first_data    = 'a';
$std->second_data   = 'b';
$std->third_data    = 'c';
$objectStd          = new ClassKernel\Data\Object(['data' => $std]);

$xml = '<?xml version="1.0" encoding="UTF-8"?>
<root>
    <to>aaaa</to>
    <from>
        <t>dddd</t>
        <t>eeee</t>
    </from>
    <heading>bbbbbb</heading>
    <body>ccccc</body>
    <element>attr</element>
</root>
';
$objectSimpleXml = new ClassKernel\Data\Object([
    'data' => $xml,
    'type' => 'simple_xml'
]);

$xml = '<?xml version="1.0" encoding="UTF-8"?>
<root>
    <to>aaaa</to>
    <from>
        <t>dddd</t>
        <t>eeee</t>
    </from>
    <heading>bbbbbb</heading>
    <body>ccccc</body>
    <element attribute="some attribute" at="next">attr</element>
</root>
';
$objectXml = new ClassKernel\Data\Object([
    'data' => $xml,
    'type' => 'xml'
]);
?>

<h5>Change existing key with the same data</h5>
<code>
<pre>$objectArray->setFirstData('a');
var_dump($objectArray->dataChanged());
$objectArray->setFirstData('b');
var_dump($objectArray->dataChanged());</pre>
</code>
<?php
$objectArray->setFirstData('a');
echo '<pre>';
var_dump($objectArray->dataChanged());
echo '</pre>';
$objectArray->setFirstData('b');
echo '<pre>';
var_dump($objectArray->dataChanged());
echo '</pre>';
?>

<h5>Created objects dump</h5>
<?php
echo '<pre>';
var_dump($object->get());
var_dump($objectArray->get());
var_dump($objectJson->get());
var_dump($objectSerialized->get());
var_dump($objectStd->get());
var_dump($objectSimpleXml->get());
var_dump($objectXml->get());
echo '</pre>';
