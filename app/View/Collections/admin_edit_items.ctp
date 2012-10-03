<?php echo $this->element('collections_admin_edit_header'); ?>

<div class="row">
	<div class="span12">
		<span class="pull-right">
			<?php echo $this->element('search_box', array('search_url' => $this->Html->url($this->request->here))); ?>
		</span>
		<?php echo $this->Html->link(__('Add Item'), array('controller' => 'videos', 'action' => 'add', 'admin' => true, $collection['Collection']['id']), array('class' => 'btn btn-success')); ?>

		<?php if(empty($targets)): ?>
			<p style="margin: 1em 0">The collection is empty.</p>
		<?php else: ?>
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="2"><?php echo __('Title'); ?></th>
					<th><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($targets as $target): 
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
					<td>
						<?php echo $this->Html->link(h($target['Target']['display_name']), array('controller' => $target_controller, 'action' => 'view', 'admin' => false, $target['Target']['id'])); ?>&nbsp;
						<?php echo !empty($target['Target']['hidden']) ? '(' . __('hidden') . ')' : ''; ?>
					</td>
					<td class="actions">
						<?php echo $this->Html->link(__('Edit'), array('controller' => $target_controller, 'action' => 'edit', 'admin' => true, $target['Target']['id']), array('class' => 'btn')); ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
	</div>
</div>
