<?php echo $this->element('collections_admin_edit_header'); ?>

<div class="row">
	<div class="span12">
		<form class="form-inline" method="post" action="<?php echo $this->Html->url(array('controller' => 'user_collections', 'action' => 'add_user', $collection_id)); ?>">
			<input type="text" class="input-medium" placeholder="Email" name="data[User][email]">
			<button type="submit" class="btn">Add to Collection</button>
		</form>

		<table id="users_table" class="table table-striped">
			<thead>
				<tr>
					<th><?php echo __('Id'); ?></th>
					<th><?php echo __('Name'); ?></th>
					<th><?php echo __('Email'); ?></th>
					<th><?php echo __('Role'); ?></th>
					<th><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($user_collections as $user): ?>
					<?php if($user['User']['id'] !== $user_id): ?>
					<tr>
						<td><?php echo h($user['User']['id']); ?></td>
						<td><?php echo h($user['User']['name']); ?></td>
						<td><?php echo h($user['User']['email']); ?></td>
						<td><?php echo h($user['Role']['display_name']); ?></td>
						<td class="actions">
							<button class="btn" data-action="edit" data-id="<?php echo $user['UserCollection']['id']; ?>"><?php echo __('Edit'); ?></button>
							<button class="btn btn-danger" data-action="delete" data-id="<?php echo $user['UserCollection']['id']; ?>"><?php echo __('Remove'); ?></button>
						</td>
					</tr>
					<?php endif; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>


<div id="editUserModal" class="modal hide fade" tabindex="-1" role="dialog">
	<form id="edit_user_form" method="post" action="<?php echo $this->Html->url(array('controller' => 'user_collections', 'action' => 'edit_user', 'admin' => true, $collection_id)); ?>">
		<input id="user_collection_edit_id" type="hidden" name="data[UserCollection][id]" value="" />
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h3>Edit User Permissions</h3>
		</div>
		<div class="modal-body">
				<div class="control-group">
					<b>Name</b>
					<div class="controls">
						<span id="user_collection_edit_name" class="input-xlarge"></span>
					</div>
				</div>
				<div class="control-group">
					<b>Email</b>
					<div class="controls">
						<span id="user_collection_edit_email" class="input-xlarge"></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><b>Role</b></label>
					<div id="user_collection_edit_role" class="controls">
					<?php foreach($roles as $role): ?>
						<label class="radio">
							<input type="radio" name="data[UserCollection][role_id]" value="<?php echo $role['Role']['id']; ?>" />
							<?php echo $role['Role']['display_name']; ?>
						</label>
					<?php endforeach; ?>
					</div>
				</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary"><?php echo __('Save Changes'); ?></button>
			<button class="btn" data-dismiss="modal"><?php echo __('Cancel'); ?></button>
		</div>
	</form>	
</div>

<div id="deleteUserModal" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h3>Remove User</h3>
	</div>
	<div class="modal-body">
		<p>Are you sure you want to remove <span id="user_collection_delete_name" style="font-weight:bold"></span> from the collection?</p>
	</div>
	<div class="modal-footer">
		<form id="delete_user_form" method="post" action="<?php echo $this->Html->url(array('controller' => 'user_collections', 'action' => 'delete_user', 'admin' => true, $collection_id)); ?>">
			<input id="user_collection_delete_id" type="hidden" name="data[UserCollection][id]" value="" />
			<button class="btn btn-danger">Yes, Remove from collection</button>
			<button class="btn" data-dismiss="modal"><?php echo __('Cancel'); ?></button>
		</form>
	</div>	
</div>

<script>
$(Catool.script('collection-edit-permissions', { 
	data: <?php echo json_encode($user_collections_by_id); ?>
}));
</script>
