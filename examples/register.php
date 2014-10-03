<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../src/Base/BlueObject.php';
require_once __DIR__ . '/../src/Base/Register.php';
require_once __DIR__ . '/../src/Data/Object.php';
require_once __DIR__ . '/../src/Data/Xml.php';

use ClassKernel\Base\Register;
?>
<div class="example">
    <h3>Base Register usage</h3>
    <div>
        <h5>Initialize Register (not required)</h5>
        <code>
            <pre>Register::initialize()</pre>
        </code>
        <?php Register::initialize()?>

        <h5>Get simple object</h5>
        <code>
            <pre>$object = Register::getObject('ClassKernel\Data\Object')
var_dump($object->getData())
$objectData = Register::getObject('ClassKernel\Data\Object', ['some_key' => 'some value'])
var_dump($objectData->getData())</pre>
        </code>
        <?php $object = Register::getObject('ClassKernel\Data\Object')?>
        <pre><?php var_dump($object->getData())?></pre>
        <?php $objectData = Register::getObject('ClassKernel\Data\Object', ['some_key' => 'some value'])?>
        <pre><?php var_dump($objectData->getData())?></pre>

        <h5>Get singleton object</h5>
        <code>
            <pre>$objectData = Register::getSingleton('ClassKernel\Data\Object', ['some_key' => 'some value'])
var_dump($objectData->getData())
$objectData = Register::getSingleton('ClassKernel\Data\Object')
var_dump($objectData->getData())
$objectData = Register::getSingleton('ClassKernel\Data\Object', ['some_key' => 'some value for special key'], 'special_key')
var_dump($objectData->getData())
$objectData = Register::getSingleton('special_key')
var_dump($objectData->getData())</pre>
        </code>
        <?php $objectData = Register::getSingleton('ClassKernel\Data\Object', ['some_key' => 'some value'])?>
        <pre><?php var_dump($objectData->getData())?></pre>
        <?php $objectData = Register::getSingleton('ClassKernel\Data\Object')?>
        <pre><?php var_dump($objectData->getData())?></pre>
        <?php $objectData = Register::getSingleton('ClassKernel\Data\Object', ['some_key' => 'some value for special key'], 'special_key')?>
        <pre><?php var_dump($objectData->getData())?></pre>
        <?php $objectData = Register::getSingleton('special_key')?>
        <pre><?php var_dump($objectData->getData())?></pre>
    </div>
    <h3>Objects information</h3>
    <div>
        <h5>Base objects info (how many times object was called including singletons)</h5>
        <code>
            <pre>var_dump(Register::getClassCounter())</pre>
        </code>
        <pre><?php var_dump(Register::getClassCounter())?></pre>

        <h5>Called singleton objects</h5>
        <code>
            <pre>var_dump(Register::getRegisteredObjects())</pre>
        </code>
        <pre><?php var_dump(Register::getRegisteredObjects())?></pre>
    </div>
    <h3>Destroy singleton object</h3>
    <div>
        <code>
            <pre>var_dump(Register::getClassCounter())
var_dump(Register::getRegisteredObjects())
Register::destroy('special_key')
var_dump(Register::getRegisteredObjects())
var_dump(Register::getClassCounter())</pre>
        </code>
        <pre><?php var_dump(Register::getClassCounter())?></pre>
        <pre><?php var_dump(Register::getRegisteredObjects())?></pre>
        <?php Register::destroy('special_key')?>
        <pre><?php var_dump(Register::getRegisteredObjects())?></pre>
        <pre><?php var_dump(Register::getClassCounter())?></pre>
    </div>
</div>
<?php
require_once __DIR__ . '/footer.php';
