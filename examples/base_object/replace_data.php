<h5>Set new value for key</h5>
<code>
<pre>$object->setFirstData('a');
echo $object->getFirstData();
$object->setData('second_data', 'b');
cho $object->getData('second_data');</pre>
</code>
<?php $object->setFirstData('a');?>
<pre><?php echo $object->getFirstData();?></pre>
<?php $object->setData('second_data', 'b');?>
<pre><?php echo $object->getData('second_data');?></pre>

<h5>Check that data was changed</h5>
<code>
<pre>var_dump($object->hasDataChanged());
var_dump($objectArray->hasDataChanged());</pre>
</code>
<pre><?php var_dump($object->hasDataChanged());?></pre>
<pre><?php var_dump($objectArray->hasDataChanged());?></pre>

<h5>Check that data in given key was changed</h5>
<code>
<pre>var_dump($object->keyDataChanged('second_data'));
var_dump($objectArray->keyDataChanged('third_data'));</pre>
</code>
<pre><?php var_dump($object->keyDataChanged('second_data'));?></pre>
<pre><?php var_dump($objectArray->keyDataChanged('third_data'));?></pre>

<h5>Get original data</h5>
<code>
<pre>$objectArray->setFirstData(1);
$objectArray->setSecondData(2);
echo $object->getOriginalData('second_data');
echo $object->getOriginalData('first_data');
var_dump($objectArray->getOriginalData());</pre>
</code>
<?php $objectArray->setFirstData(1);?>
<?php $objectArray->setSecondData(2);?>
<pre><?php echo $objectArray->getOriginalData('second_data');?></pre>
<pre><?php echo $objectArray->getOriginalData('first_data');?></pre>
<pre><?php var_dump($objectArray->getOriginalData());?></pre>

<h5>Restore data for single key</h5>
<code>
<pre>$objectArray->restoreData('first_data');
echo $objectArray->getFirstData();</pre>
</code>
<?php $objectArray->restoreData('first_data');?>
<pre><?php echo $objectArray->getFirstData();?></pre>

<h5>Restore data for whole object</h5>
<code>
<pre>$objectArray->restoreData()
var_dump($objectArray->getData());</pre>
</code>
<?php $objectArray->restoreData()?>
<pre><?php var_dump($objectArray->getData());?></pre>

<h5>Set changed data as original data</h5>
<code>
<pre>$objectArray->setFirstData(1);
$objectArray->setSecondData(2);
$objectArray->replaceDataArrays()
var_dump($objectArray->hasDataChanged());
var_dump($objectArray->getData());
var_dump($objectArray->getOriginalData());</pre>
</code>
<?php $objectArray->setFirstData(1);?>
<?php $objectArray->setSecondData(2);?>
<?php $objectArray->replaceDataArrays()?>
<pre><?php var_dump($objectArray->hasDataChanged());?></pre>
<pre><?php var_dump($objectArray->getData());?></pre>
<pre><?php var_dump($objectArray->getOriginalData());?></pre>