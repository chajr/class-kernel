<code>
<pre>$objectArray->setAnotherData('another')
$objectJson->setNewData('data')
var_dump($objectArray->getData())
var_dump($objectJson->getData())
$objectJson->mergeBlueObject($objectArray)
var_dump($objectJson->getData())</pre>
</code>
<?php $objectArray->setAnotherData('another')?>
<?php $objectJson->setNewData('data')?>
<pre><?php var_dump($objectArray->getData());?></pre>
<pre><?php var_dump($objectJson->getData());?></pre>
<?php $objectJson->mergeBlueObject($objectArray)?>
<pre><?php var_dump($objectJson->getData());?></pre>
