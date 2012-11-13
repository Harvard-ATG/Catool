<?php echo $this->element('breadcrumbs', array('crumbs' => array(
	array(__('Home'), '/'),
	array(__('Manage Collections'), '/admin/collections'),
	array(__('Create Collection'), $this->Html->request->here(false))
))); ?>

<div class="page-header">
	<h1><?php echo __('Create Collection'); ?></h1>
</div>
<div class="row">
	<div class="span6">
	<?php echo $this->Form->create('Collection');?>
		<fieldset>
			<?php
				echo $this->Form->input('display_name');
				echo $this->Form->label('display_description');
				echo $this->Form->textarea('display_description');
			?>
		</fieldset>
		<div class="form-actions">
			<?php echo $this->Form->button('Submit', array('class' => 'btn btn-primary')); ?>
			<?php echo $this->Html->link('Cancel', '/admin/collections', array('class' => 'btn')); ?>
		</div>
	<?php echo $this->Form->end(); ?>
	</div>
</div>
