<h5>By set methods</h5>
<code>
<pre>$object = new ClassKernel\Data\Object();
$object->setFirstData(1);
$object->setSecondData(2);
$object->setData('third_data', 3);
$object->setData([
    'fourth_data' => 4,
    'fifth_data'  => 5
]);</pre>
</code>
<?php
$object = new ClassKernel\Data\Object();
$object->setFirstData(1);
$object->setSecondData(2);
$object->setData('third_data', 3);
$object->setData([
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

$std = new stdClass();
$std->first_data    = 'a';
$std->second_data   = 'b';
$std->third_data    = 'c';
$serialized = serialize($std);
$objectSerialized = new ClassKernel\Data\Object([
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
?>

<h5>Created objects dump</h5>
<?php
echo '<pre>';
var_dump($object->getData());
var_dump($objectArray->getData());
var_dump($objectJson->getData());
var_dump($objectSerialized->getData());
var_dump($objectStd->getData());
echo '</pre>';
