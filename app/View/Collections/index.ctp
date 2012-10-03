<?php echo $this->element('breadcrumbs', array('crumbs' => array(
	array(__('Home'), '/'),
	array(__('My Collections'), $this->Html->request->here)
))); ?>

<div class="page-header">
	<?php if($hasManagePermission): ?>
		<span class="pull-right">
			<?php echo $this->Html->link(__('Manage Collections'), array('action' => 'index', 'admin' => true), array('class' => 'btn')); ?>
		</span>
	<?php endif; ?>
	<h1><?php echo __('My Collections');?></h1>
</div>
	
<div class="row">
	<div class="span12">
	<?php if(empty($collections)): ?>
		<p>No collections to display</p>
	<?php endif; ?>
	</div>
</div>

<?php foreach ($collections as $collection): ?>
	<div class="row">
		<div class="span9 well">
			<h3><?php echo $this->Html->link(h($collection['Collection']['display_name']), array('action' => 'view', $collection['Collection']['id'])); ?></h3>
			<p><?php echo nl2br($collection['Collection']['display_description']); ?></p>
		</div>
	</div>
<?php endforeach; ?>
