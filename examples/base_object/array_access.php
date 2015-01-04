<h5>Set and get data from object</h5>
<code>
<pre>$ob                 = new ClassKernel\Data\Object(['existing' => 'bla bla bla']);
$ob['first_key']    = 'first data key';
$ob['second_key']   = 'second data key';
$ob['third_key']    = 'third data key';
var_dump($ob->get());
var_dump($ob['existing']);
var_dump($ob['third_key']);
</pre>
</code>
<?php
$ob                 = new ClassKernel\Data\Object(['existing' => 'bla bla bla']);
$ob['first_key']    = 'first data key';
$ob['second_key']   = 'second data key';
$ob['third_key']    = 'third data key';
?>
<pre><?php var_dump($ob->get());?></pre>
<pre><?php var_dump($ob['existing']);?></pre>
<pre><?php var_dump($ob['third_key']);?></pre>

<h5>loop usage</h5>
<code>
<pre>foreach ($ob as $key => $val) {
    var_dump($key, $val);
    echo "\n";
}</pre>
</code>
<pre><?php
foreach ($ob as $key => $val) {
    var_dump($key, $val);
    echo "\n";
}
?></pre>