<h5>Set rules and check that exists</h5>
<code>
<pre>$validateObject = new ClassKernel\Data\Object()
$validateObject->putValidationRule('#^test_[\w]+#', '#^[\d]{2}$#');
$validateObject->putValidationRule('#[\w]+_new$#', '#^[a-z]+$#');
$validateObject->putValidationRule('#special_key#', '#^[a-z]+$#');
$validateObject->returnValidationRule()</pre>
</code>
<?php $validateObject = new ClassKernel\Data\Object()?>
<?php $validateObject->putValidationRule('#^test_[\w]+#', '#^[\d]{2}$#');?>
<?php $validateObject->putValidationRule('#[\w]+_new$#', '#^[a-z]+$#');?>
<?php $validateObject->putValidationRule('#special_key#', '#^[a-z]+$#');?>
<pre><?php var_dump($validateObject->returnValidationRule())?></pre>

<h5>Set data with comparison</h5>
<code>
<pre>$validateObject->setTestValid(22);
$validateObject->setTestInvalid(234234);
$validateObject->setValidNew('some_data');
$validateObject->setInvalidNew('Lorem ipsum');
$validateObject->setSpecialKey('special_key_data');
var_dump($validateObject->getObjectError())</pre>
</code>
<?php $validateObject->setTestValid(22);?>
<?php $validateObject->setTestInvalid(234234);?>
<?php $validateObject->setValidNew('some_data');?>
<?php $validateObject->setInvalidNew('Lorem ipsum');?>
<?php $validateObject->setSpecialKey('special_key_data');?>
<pre><?php var_dump($validateObject->getObjectError())?></pre>