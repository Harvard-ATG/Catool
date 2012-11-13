<?php 
echo $this->element('breadcrumbs', array('crumbs' => array(
	array(__('Home'), '/'),
	array(__('Manage Collections'), '/admin/collections'),
	array(__('Edit Collection'), $this->Html->request->here(false))
))); 

$tabs = array(
	array('Settings', array('controller' => 'collections', 'action' => 'edit', 'admin' => true, $collection_id)),
	array('Permissions', array('controller' => 'collections', 'action' => 'edit_permissions', 'admin' => true, $collection_id)),
	array('Items', array('controller' => 'collections', 'action' => 'edit_items', 'admin' => true, $collection_id))
);
?>

<div class="page-header">
	<h1><?php echo __('Edit Collection'); ?>
		<small><?php echo h($collection['Collection']['display_name']); ?></small>
	</h1>
</div>

<div class="row">
	<div class="span12">
		<ul class="nav nav-tabs">
			<?php foreach($tabs as $tab): ?>
				<li <?php echo $this->Html->request->here === Router::url($tab[1]) ? 'class="active"' : ''; ?>>
					<?php echo $this->Html->link($tab[0], $tab[1]); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
