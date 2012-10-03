<?php echo $this->element('breadcrumbs', array('crumbs' => array(
	array(__('Home'), '/'),
	array(__('My Collections'), '/collections'),
	array($this->Text->truncate($collection['Collection']['display_name'], 25), array('controller' => 'collections', 'action' => 'view', $collection_id)), 
	array(__('Posts'), $this->Html->request->here)
))); ?>

<div class="page-header">
	<h1><?php echo __('All Users With Posts'); ?></h1>
</div>

<div class="row">
	<div class="span3">
		<div class="well">
			<ul id="users_list" class="nav nav-list">
				<li class="nav-header"><?php echo __('Users'); ?></li>
				<li class="<?php echo empty($user_id) ? 'active' : ''; ?>">
					<?php $user_title = __('Everyone') . ' ('.$users['total_notes'].')'; ?>
					<?php echo $this->Html->link($user_title, array(
						'controller' => 'collections', 
						'action' => 'posts', 
						$collection_id)); 
					?>
				</li>
				<li class="divider"></li>

				<?php foreach($users['users'] as $user): ?>
					<li class="<?php echo !empty($user_id) && $user_id === $user['id'] ? 'active' : ''; ?>">
						<?php $user_title = $user['name'] .' ('. $user['num_notes'] .')'; ?>
						<?php echo $this->Html->link($user_title, array(
								'controller' => 'collections', 
								'action' => 'posts',
								'?' => array('user_id' => $user['id']),
								$collection_id
							)); 
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<div class="span9">
		<table id="posts_table" class="table table-striped">
			<thead>
				<tr>
					<th><?php echo __('Title'); ?></th>
					<th><?php echo __('Date'); ?></th>
					<th><?php echo __('Author'); ?></th>
					<th><?php echo __('Item'); ?></th>
					<th><?php echo __('Collection'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($notes as $note): ?>
					<tr>
						<td>
							<?php echo $this->Html->link(h($note['Note']['title']), array(
									'controller' => $this->TargetLink->controllerName($note['Target']['type']),
									'action' => 'view',
									'?' => array('note_id' => $note['Note']['id']),
									$note['Target']['id'])); 
							?>
						</td>
						<td>
							<?php echo $this->Time->format('Y-m-d H:i', $note['Note']['created']); ?>
						</td>
						<td>
							<?php echo $this->Html->link(h($note['User']['name']), array(
									'controller' => 'collections',
									'action' => 'posts',
									'?' => array('user_id' => $note['User']['id']),
									$note['Collection']['id'])); 
							?>
						</td>
						<td>
							<?php echo $this->Html->link(h($note['Target']['display_name']), array(
									'controller' => $this->TargetLink->controllerName($note['Target']['type']),
									'action' => 'view',
									$note['Target']['id'])); 
							?>
						</td>
						<td>
							<?php echo $this->Html->link(h($note['Collection']['display_name']), array(
									'controller' => 'collections',
									'action' => 'view',
									$note['Collection']['id'])); 
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<script>
$(Catool.script('collection-posts'));
</script>
