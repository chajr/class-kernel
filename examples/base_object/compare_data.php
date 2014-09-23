<h5>Compare 2 Objects data</h5>
<code>
<pre>var_dump($objectJson->compareData('third_data', $objectArray->getThirdData()));
var_dump($objectJson->compareData('first_data', $objectArray->getFirstData()));</pre>
</code>
<pre><?php var_dump($objectJson->compareData('third_data', $objectArray->getThirdData()));?></pre>
<pre><?php var_dump($objectJson->compareData('first_data', $objectArray->getFirstData()));?></pre>

<h5>Compare 2 Objects data, list of keys</h5>
<code>
<pre></pre>
</code>
<pre><?php var_dump($objectJson->compareData($objectJson->getData(), $objectArray->getFirstData()));?></pre>
<pre><?php var_dump($objectJson->compareData($objectJson->getData(), $objectSerialized->getFirstData()));?></pre>

<h5>Compare 2 Objects</h5>
<code>
<pre></pre>
</code>