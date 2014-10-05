<h5>Create xml data</h5>
<code>
<pre>$xml                = new ClassKernel\Data\Xml();
$xml->formatOutput  = true;
$root               = $xml->createElement('root');
$child              = $xml->createElement('child');

$child->setAttribute('attribute', 'test attribute value');
$root->appendChild($child);
$xml->appendChild($root);

echo htmlspecialchars($xml->saveXML())</pre>
</code>
<?php
$xml                = new ClassKernel\Data\Xml();
$xml->formatOutput  = true;
$root               = $xml->createElement('root');
$child              = $xml->createElement('child');

$child->setAttribute('attribute', 'test attribute value');
$root->appendChild($child);
$xml->appendChild($root);
?>
<pre><?php echo htmlspecialchars($xml->saveXML());?></pre>
