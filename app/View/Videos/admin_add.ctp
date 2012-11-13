<?php echo $this->element('breadcrumbs', array('crumbs' => array(
	array(__('Home'), '/'),
	array(__('Manage Collections'), '/admin/collections'),
	array(__('Edit Collection'), '/admin/collections/edit/'.$collection_id),
	array(__('Add Video'), $this->Html->request->here(false))
))); ?>

<div class="page-header">
	<h1><?php echo __('Add Video'); ?></h1>
</div>
<div class="row">
	<div class="span12">
		<?php echo $this->element('video_edit', array(
			'collection_id' => $collection_id,
			'action_url' => $this->Html->url(array(
				'controller' => 'videos', 
				'action' => 'add', 
				'admin' => true, 
				$collection_id
			))
		)); ?>
	</div>
</div>
