<h5>Create xml data from file</h5>
<code>
<pre>$xml                = new ClassKernel\Data\Xml();
$xml->formatOutput  = true;

$xml->loadXmlFile(__DIR__ . '/source.xml');
echo htmlspecialchars($xml->saveXML());</pre>
</code>
<?php
$xml                = new ClassKernel\Data\Xml();
$xml->formatOutput  = true;

$xml->loadXmlFile(__DIR__ . '/source.xml');
?>
<pre><?php echo htmlspecialchars($xml->saveXML());?></pre>

<h5>Create xml data from string</h5>
<code>
<pre>$xmlData            = file_get_contents(__DIR__ . '/source.xml');
$xml                = new ClassKernel\Data\Xml();
$xml->formatOutput  = true;

$xml->loadXML($xmlData);
echo htmlspecialchars($xml->saveXML());</pre>
</code>
<?php
$xmlData            = file_get_contents(__DIR__ . '/source.xml');
$xml                = new ClassKernel\Data\Xml();
$xml->formatOutput  = true;

$xml->loadXML($xmlData);
?>
<pre><?php echo htmlspecialchars($xml->saveXML());?></pre>