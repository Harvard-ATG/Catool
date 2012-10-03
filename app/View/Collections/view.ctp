<?php echo $this->element('breadcrumbs', array('crumbs' => array(
	array(__('Home'), '/'),
	array(__('My Collections'), '/collections'),
	array($this->Text->truncate($collection['Collection']['display_name'], 25), $this->Html->request->here)
))); ?>

<div class="page-header">
	<h1><?php echo h($collection['Collection']['display_name']); ?></h1>
</div>
<div class="row">
	<div class="span12">
		<div class="pull-right">
			<?php echo $this->element('search_box', array('search_url' => $this->Html->url($this->request->here))); ?>
		</div>
		<div class="pull-right" style="margin-right: 1em">
			<?php echo $this->Html->link(__('Posts by User'), array('controller' => 'collections', 'action' => 'posts', $collection['Collection']['id']), array('class' => 'btn')); ?>
		</div>
		<br/>
		<table class="table table-striped" style="margin-top: 1em">
			<thead>
				<tr>
					<th width="50%" colspan="2"><?php echo __('Title'); ?></th>
					<th><?php echo __('Last Post'); ?></th>
					<th><?php echo __('Total Posts'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($targets as $target): 
					// always skip hidden targets, admins can view these on admin view
					if(!empty($target['Target']['hidden'])) {
						continue;
					}

					$target_id = $target['Target']['id']; 
					$target_controller = $this->TargetLink->controllerName($target['Target']['type']); 
					$target_icon_cls = $this->TargetLink->iconClass($target['Target']['type']);

					$last_post = $note_stats_for[$target_id]['note_last_post'];
					if(isset($last_post)) {
						$last_post = $this->Time->niceShort($last_post);
					}

				?>
				<tr>
					<td><i class="icon-<?php echo $target_icon_cls; ?>"></i></td>
					<td><?php echo $this->Html->link(h($target['Target']['display_name']), array('controller' => $target_controller, 'action' => 'view', $target['Target']['id'])); ?>&nbsp;</td>
					<td><?php echo h($last_post); ?> &nbsp;</td>
					<td><?php echo $target['Target']['note_count']; ?>&nbsp;</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
