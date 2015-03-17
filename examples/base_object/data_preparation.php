<h5>Create test objects/functions</h5>
<code>
<pre>function someFunction($key, $value, $parent) {
    return $value . ' - ' . $key . ' - ' . get_class($parent);
}

class dataChanger
{
    static function testOne($key, $value, $parent)
    {
        return $value . ' - ' . $key . ' - ' . get_class($parent);
    }

    public function testTwo($key, $value, $parent)
    {
        return $value . ' - ' . $key . ' - ' . get_class($parent);
    }
}

$dataChanger = new dataChanger();
$testObject     = new ClassKernel\Data\Object();</pre>
</code>
<?php
function someFunction($key, $value, $parent) {
    return $value . ' - ' . $key . ' - ' . get_class($parent);
}

class dataChanger
{
    static function testOne($key, $value, $parent)
    {
        return $value . ' - ' . $key . ' - ' . get_class($parent);
    }

    public function testTwo($key, $value, $parent)
    {
        return $value . ' - ' . $key . ' - ' . get_class($parent);
    }
}

$dataChanger    = new dataChanger();
$testObject     = new ClassKernel\Data\Object();
?>

<h5>Set data</h5>
<code>
<pre>$testObject->putPreparationCallback([
    '#test_key#'    => 'someFunction',
    '#^test_[\w]+#' => [$dataChanger, 'testTwo'],
    '#nn#'          => 'dataChanger::testOne',
    '#callback#'    => function ($key, $value, $parent) {
        return $value . ' - ' . $key . ' - ' . get_class($parent);
    }
]);

$testObject->setTestKey('test data');
$testObject->setTestNext('test data next');
$testObject->setNn('test nn');
$testObject->setCallback('test callback');
var_dump($testObject->get());</pre>
</code>
<?php
$testObject->putPreparationCallback([
    '#test_key#'    => 'someFunction',
    '#^test_[\w]+#' => [$dataChanger, 'testTwo'],
    '#nn#'          => 'dataChanger::testOne',
    '#callback#'    => function ($key, $value, $parent) {
        return $value . ' - ' . $key . ' - ' . get_class($parent);
    }
]);

$testObject->setTestKey('test data');
$testObject->setTestNext('test data next');
$testObject->setNn('test nn');
$testObject->setCallback('test callback');
?>
<pre><?php var_dump($testObject->get());?></pre>

<h5>Get data</h5>
<code>
<pre>$testObject->putReturnCallback([
    '#test_key#'    => 'someFunction',
    '#^test_[\w]+#' => [$dataChanger, 'testTwo'],
    '#nn#'          => 'dataChanger::testOne',
    '#callback#'    => function ($key, $value) {
        return $value . ' - changed';
    }
]);
var_dump($testObject->getData());</pre>
</code>
<?php
$testObject->putReturnCallback([
    '#test_key#'    => 'someFunction',
    '#^test_[\w]+#' => [$dataChanger, 'testTwo'],
    '#nn#'          => 'dataChanger::testOne',
    '#callback#'    => function ($key, $value) {
        return $value . ' - changed';
    }
]);
?>
<pre><?php var_dump($testObject->getTestKey());?></pre>
<pre><?php var_dump($testObject->getTestNext());?></pre>
<pre><?php var_dump($testObject->getNn());?></pre>
<pre><?php var_dump($testObject->getCallback());?></pre>