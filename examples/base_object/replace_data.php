<h5>Set new value for key</h5>
<code>
<pre>$object->setFirstData('a');
echo $object->getFirstData();
$object->set('second_data', 'b');
echo $object->get('second_data');</pre>
</code>
<?php $object->setFirstData('a');?>
<pre><?php echo $object->getFirstData();?></pre>
<?php $object->set('second_data', 'b');?>
<pre><?php echo $object->get('second_data');?></pre>

<h5>Check that data was changed</h5>
<code>
<pre>var_dump($object->dataChanged());
var_dump($objectArray->dataChanged());</pre>
</code>
<pre><?php var_dump($object->dataChanged());?></pre>
<pre><?php var_dump($objectArray->dataChanged());?></pre>

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
echo $object->returnOriginalData('second_data');
echo $object->returnOriginalData('first_data');
var_dump($objectArray->returnOriginalData());</pre>
</code>
<?php $objectArray->setFirstData(1);?>
<?php $objectArray->setSecondData(2);?>
<pre><?php echo $objectArray->returnOriginalData('second_data');?></pre>
<pre><?php echo $objectArray->returnOriginalData('first_data');?> - (Data was not exist before change)</pre>
<pre><?php var_dump($objectArray->returnOriginalData());?></pre>

<h5>Restore data for single key</h5>
<code>
<pre>$objectArray->restore('first_data');
echo $objectArray->getFirstData();</pre>
</code>
<?php $objectArray->restore('first_data');?>
<pre><?php echo $objectArray->getFirstData();?></pre>

<h5>Restore data for whole object</h5>
<code>
<pre>$objectArray->restore()
var_dump($objectArray->getData());</pre>
</code>
<?php $objectArray->restore()?>
<pre><?php var_dump($objectArray->getData());?></pre>

<h5>Set changed data as original data</h5>
<code>
<pre>$objectArray->setFirstData(1);
$objectArray->setSecondData(2);
$objectArray->replaceDataArrays()
var_dump($objectArray->dataChanged());
var_dump($objectArray->get());
var_dump($objectArray->returnOriginalData());</pre>
</code>
<?php $objectArray->setFirstData(1);?>
<?php $objectArray->setSecondData(2);?>
<?php $objectArray->replaceDataArrays()?>
<pre><?php var_dump($objectArray->dataChanged());?></pre>
<pre><?php var_dump($objectArray->get());?></pre>
<pre><?php var_dump($objectArray->returnOriginalData());?></pre>