<dl>
<?php if(isset($title) && $title): ?>
	<h3><?php echo $title; ?></h3>
<?php endif; ?>

<?php
foreach($info as $key => $value):
	if(!empty($value)): 
?> 
	<dt><?php echo $key; ?></dt>
	<dd><?php echo $value; ?></dd>
<?php
	endif;
endforeach;
?>
</dl>