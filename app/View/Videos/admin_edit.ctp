<?php echo $this->element('breadcrumbs', array('crumbs' => array(
	array(__('Home'), '/'),
	array(__('Manage Collections'), '/admin/collections'),
	array(__('Edit Collection'), '/admin/collections/edit/'.$video['Video']['collection_id']),
	array(__('Edit Video'), $this->Html->request->here)
))); ?>

<div class="page-header">
	<h1><?php echo __('Edit Video'); ?></h1>
</div>
<div class="row">
	<div class="span12">
		<?php echo $this->element('video_edit', array(
			'video' => $video,
			'collection_id' => $video['Video']['collection_id'],
			'action_url' => $this->Html->url(array(
				'controller' => 'videos', 
				'action' => 'edit', 
				'admin' => true, 
				$video['Video']['id']
			))
		)); ?>
	</div>
</div>
