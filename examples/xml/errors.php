<h5>Lod file error</h5>
<code>
<pre>$xml                = new ClassKernel\Data\Xml();
$xml->formatOutput  = true;

$xml->loadXmlFile(__DIR__ . '/no_existing_file.xml');

if ($xml->hasErrors()) {
    echo $xml->getError();
}</pre>
</code>
<?php
$xml                = new ClassKernel\Data\Xml();
$xml->formatOutput  = true;

$xml->loadXmlFile(__DIR__ . '/no_existing_file.xml');
?>
<pre><?php
    if ($xml->hasErrors()) {
        echo $xml->getError();
    }
?></pre>
