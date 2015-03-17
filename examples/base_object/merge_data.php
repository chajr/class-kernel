<code>
<pre>$objectArray->setAnotherData('another')
$objectJson->setNewData('data')
var_dump($objectArray->get())
var_dump($objectJson->get())
$objectJson->mergeBlueObject($objectArray)
var_dump($objectJson->get())</pre>
</code>
<?php $objectArray->setAnotherData('another')?>
<?php $objectJson->setNewData('data')?>
<pre><?php var_dump($objectArray->get());?></pre>
<pre><?php var_dump($objectJson->get());?></pre>
<?php $objectJson->mergeBlueObject($objectArray)?>
<pre><?php var_dump($objectJson->get());?></pre>
