<?php echo $this->element('breadcrumbs', array('crumbs' => array(
	array(__('Home'), '/'),
	array(__('Manage Collections'), $this->Html->request->here)
))); ?>

<div class="page-header">
	<span class="pull-right">
		<?php echo $this->Html->link(__('Return to Collections'), array('action' => 'index', 'admin' => false), array('class' => 'btn')); ?>
	</span>
	<h1><?php echo __('Manage Collections');?></h1>
</div>

<div class="row">
	<div class="span12">

	<?php if($hasCreatePermission): ?>
		<div class="row">
			<div class="span9" style="margin-bottom: 1em">
				<?php echo $this->Html->link(__('Create Collection'), array('action' => 'add', 'admin' => true), array('class' => 'btn btn-success')); ?>
			</div>
		</div>
	<?php endif; ?>
	
	<?php if(empty($collections)): ?>
		<p>No collections to display</p>
	<?php else: ?>
		<?php foreach ($collections as $collection): ?>
		<div class="row">
			<div class="span9 well">
				<span class="pull-right">
					<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $collection['Collection']['id']), array('class' => 'btn')); ?>
				</span>
				<h3><?php echo $this->Html->link(h($collection['Collection']['display_name']), array('action' => 'view', 'admin' => false, $collection['Collection']['id'])); ?></h3>
				<p><?php echo nl2br($collection['Collection']['display_description']); ?></p>
			</div>
		</div>
		<?php endforeach; ?>
	<?php endif; ?>
	</div>
</div>

