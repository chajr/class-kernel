<h5>By has methods</h5>
<code>
<pre>var_dump($object->hasFirstData());
var_dump($object->hasNonExistData());</pre>
</code>
<pre><?php var_dump($object->hasFirstData());?></pre>
<pre><?php var_dump($object->hasNonExistData());?></pre>

<h5>By access to data</h5>
<code>
<pre>var_dump($object->getFirstData());
var_dump($object->getNonExistData());
var_dump($object->get('non_exist_data'));</pre>
</code>
<pre><?php var_dump($object->getFirstData());?></pre>
<pre><?php var_dump($object->getNonExistData());?></pre>
<pre><?php var_dump($object->get('non_exist_data'));?></pre>
