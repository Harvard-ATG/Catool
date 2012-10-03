<div id="qunit"></div>
<div id="qunit-fixture"></div>

<?php 

// load qunit testing framework
echo $this->Html->css('qunit.css', 'stylesheet', array('inline' => false)); 

// load core modules that are being tested
if(isset($modules) && !empty($modules)) {
	echo $this->Html->script($modules);
}

// load test cases for modules
if(isset($tests) && !empty($tests)) {
	echo $this->Html->script($tests);
}

?>
