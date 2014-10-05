<h5>Export as JSON</h5>
<code>
<pre>echo $object->toJson();</pre>
</code>
<pre><?php echo $object->toJson();?></pre>

<h5>Export as String</h5>
<code>
<pre>echo $object</pre>
<pre>echo $object->toString('. ');</pre>
</code>
<pre><?php echo $object;?></pre>
<pre><?php echo $object->toString('. ');?></pre>

<h5>Export as serialized data</h5>
<code>
<pre>echo $object->serialize()</pre>
</code>
<pre><?php echo $object->serialize()?></pre>

<h5>Export as serialized data (skip objects inside)</h5>
<code>
<pre>$std = new stdClass();
$std->param = 1;
$std->next_param = 2;
$object->setObject($std);
echo $object->serialize()
echo $object->serialize(true)</pre>
</code>
<?php
$std = new stdClass();
$std->param = 1;
$std->next_param = 2;
$object->setObject($std);
?>
<pre><?php echo $object->serialize()?></pre>
<pre><?php echo $object->serialize(true)?></pre>
<?php $object->unsetObject()?>

<h5>Export as stdClass</h5>
<code>
<pre>var_dump($object->toStdClass())</pre>
</code>
<pre><?php var_dump($object->toStdClass())?></pre>

<h5>Export as xml</h5>
<code>
    <pre>echo htmlspecialchars($object->toXml())</pre>
</code>
<pre><?php echo htmlspecialchars($object->toXml())?></pre>
