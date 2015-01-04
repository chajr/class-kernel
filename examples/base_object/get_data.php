<h5>By key access</h5>
<code>
<pre>echo $object->getData('first_data');
echo $object->get('second_data');
var_dump($object->get());</pre>
</code>
<pre><?php echo $object->get('first_data');?></pre>
<pre><?php echo $object->get('second_data');?></pre>
<pre><?php var_dump($object->get());?></pre>

<h5>By get methods</h5>
<code>
<pre>echo $object->getFirstData();
echo $object->getSecondData();</pre>
</code>
<pre><?php echo $object->getFirstData();?></pre>
<pre><?php echo $object->getSecondData();?></pre>
