<code>
<pre>use ClassKernel\Data\Object;

function changeData($key, $value, Object $object, $methodAttributes){
    if ($key === 'first_data') {
        return 'i am first data';
    }
    return $value . '_' . $methodAttributes[0];
}
$objectArray->traveler('changeData', ['sufix'], null, true);
var_dump($objectArray->getData());
</pre>
</code>
<?php
use ClassKernel\Data\Object;

function changeData($key, $value, Object $object, $methodAttributes){
    if ($key === 'first_data') {
        return 'i am first data';
    }
    return $value . '_' . $methodAttributes[0];
}
$objectArray->traveler('changeData', ['sufix'], null, true);
?>
<pre><?php var_dump($objectArray->get());?></pre>
