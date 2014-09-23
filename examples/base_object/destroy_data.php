<h5>Destroy single key</h5>
<code>
<pre>$object->unsetData('first_data')
$object->unsetSecondData()
var_dump($object->getData())</pre>
</code>
<?php $object->unsetData('first_data')?>
<?php $object->unsetSecondData()?>
<pre><?php var_dump($object->getData())?></pre>

<h5>Destroy all keys</h5>
<code>
<pre>$object->unsetData()
var_dump($object->getData())</pre>
</code>
<?php $object->unsetData()?>
<pre><?php var_dump($object->getData())?></pre>

<h5>Set data to null</h5>
<code>
<pre> $objectArray->clearData('first_data')
$objectArray->clearSecondData()
var_dump($objectArray->getData())</pre>
</code>
<?php $objectArray->clearData('first_data') ?>
<?php $objectArray->clearSecondData() ?>
<pre><?php var_dump($objectArray->getData())?></pre>
