<h5>Compare 2 Objects data</h5>
<code>
<pre>var_dump($objectJson->compareData($objectArray->getThirdData(), 'third_data'));
var_dump($objectJson->compareData($objectArray->getFirstData(), 'first_data'));</pre>
</code>
<pre><?php var_dump($objectJson->compareData($objectArray->getThirdData(), 'third_data'));?></pre>
<pre><?php var_dump($objectJson->compareData($objectArray->getFirstData(), 'first_data'));?></pre>

<h5>Compare 2 Objects data, list of keys</h5>
<code>
<pre>var_dump($objectJson->compareData($objectJson->get()))
var_dump($objectJson->compareData($objectArray->get()))
var_dump($objectJson->compareData($objectSerialized->get()))</pre>
</code>
<pre><?php var_dump($objectJson->compareData($objectJson->get()));?></pre>
<pre><?php var_dump($objectJson->compareData($objectArray->get()));?></pre>
<pre><?php var_dump($objectJson->compareData($objectSerialized->get()));?></pre>

<h5>Compare 2 Objects</h5>
<code>
<pre>var_dump($objectJson->compareData($objectArray))
var_dump($objectJson->compareData($objectSerialized))</pre>
</code>
<pre><?php var_dump($objectJson->compareData($objectArray));?></pre>
<pre><?php var_dump($objectJson->compareData($objectSerialized));?></pre>

<h5>Compare with given operator</h5>
<code>
<pre>var_dump($objectJson->compareData($objectArray->getThirdData(), 'third_data', '!=='));
var_dump($objectJson->compareData($objectArray->getFirstData(), 'first_data', '<'));</pre>
</code>
<pre><?php var_dump($objectJson->compareData($objectArray->getThirdData(), 'third_data', '!=='));?></pre>
<pre><?php var_dump($objectJson->compareData($objectArray->getFirstData(), 'first_data', '<'));?></pre>
In second example compare char with null, be caurfle with usage of data comparation.
<pre><?php var_dump($objectArray->getFirstData(), $objectJson->get('first_data')); ?></pre>

<h5>Compare with magic methods</h5>
<code>
<pre>var_dump($objectArray->isFirstData(null));
var_dump($objectArray->notFirstData('string'));
var_dump($objectJson->isFirstData(null));</pre>
</code>
<pre><?php var_dump($objectArray->isFirstData(null));?></pre>
<pre><?php var_dump($objectArray->notFirstData('string'));?></pre>
<pre><?php var_dump($objectJson->isFirstData(null));?></pre>