<?php
$permissions = array(
	array('Users', '/admin/sites/users')
);
?>

<ul class="nav nav-list">
	<li class="nav-header">Permissions</li>
	<?php foreach($permissions as $item): ?>
		<li <?php echo Router::url($this->Html->request->here) === Router::url($item[1]) ? 'class="active"' : ''; ?>>
			<?php echo $this->Html->link($item[0], $item[1]); ?>
		</li>
	<?php endforeach; ?>
</ul>
