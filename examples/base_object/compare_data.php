<h5>Compare 2 Objects data</h5>
<code>
<pre>var_dump($objectJson->compareData('third_data', $objectArray->getThirdData()));
var_dump($objectJson->compareData('first_data', $objectArray->getFirstData()));</pre>
</code>
<pre><?php var_dump($objectJson->compareData($objectArray->getThirdData(), 'third_data'));?></pre>
<pre><?php var_dump($objectJson->compareData($objectArray->getFirstData(), 'first_data'));?></pre>

<h5>Compare 2 Objects data, list of keys</h5>
<code>
<pre>var_dump($objectJson->compareData($objectJson->getData()))
var_dump($objectJson->compareData($objectArray->getData()))
var_dump($objectJson->compareData($objectSerialized->getData()))</pre>
</code>
<pre><?php var_dump($objectJson->compareData($objectJson->getData()));?></pre>
<pre><?php var_dump($objectJson->compareData($objectArray->getData()));?></pre>
<pre><?php var_dump($objectJson->compareData($objectSerialized->getData()));?></pre>

<h5>Compare 2 Objects</h5>
<code>
<pre>var_dump($objectJson->compareData($objectArray))
var_dump($objectJson->compareData($objectSerialized))</pre>
</code>
<pre><?php var_dump($objectJson->compareData($objectArray));?></pre>
<pre><?php var_dump($objectJson->compareData($objectSerialized));?></pre>