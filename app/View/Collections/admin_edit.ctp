<?php echo $this->element('collections_admin_edit_header'); ?>

<div class="row">
	<div class="span12">
		<div id="collection_settings_form">
			<form id="edit_user_form" method="post" action="<?php echo $this->Html->url(array('controller' => 'collections', 'action' => 'edit', 'admin' => true, $collection_id)); ?>">
			<fieldset>
				<input type="hidden" name="data[Collection][id]" value="<?php echo $collection['Collection']['id']; ?>" />
				
				<div class="control-group">
					<label class="control-label" for="display_name">Collection Name</label>
					<div class="controls">
						<input type="text" id="display_name" name="data[Collection][display_name]" value="<?php echo $collection['Collection']['display_name']; ?>" />
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="display_description">Description</label>
					<div class="controls">
						<textarea id="display_description" name="data[Collection][display_description]"><?php echo $collection['Collection']['display_description']; ?></textarea>
					</div>
				</div>
			</fieldset>
			<div class="form-actions">
				<span class="pull-right">
					<button id="delete_collection_button" class="btn btn-danger"><?php echo __('Delete'); ?></button>
				</span>
				<input type="submit" name="submit" value="<?php echo __('Save Changes'); ?>" class="btn btn-primary" />
			</div>
			</form>
		</div>
	</div>
</div>

<div id="collectionDeleteModal" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-header">
		<button id="delete_collection_button" type="button" class="close" data-dismiss="modal">&times;</button>
		<h3>Delete Collection</h3>
	</div>
	<div class="modal-body">
		<p>Are you sure you want to permanently delete <b><?php echo $collection['Collection']['display_name']; ?></b>?</p>
	</div>
	<div class="modal-footer">
		<?php echo $this->Form->postLink(__('Yes, Permanently Delete'), array('controller' => 'collections', 'action' => 'delete', 'admin' => true, $this->Form->value('Collection.id')), array('class' => 'btn btn-danger')); ?>
		
		<button class="btn" data-dismiss="modal"><?php echo __('Cancel'); ?></button>
	</div>	
</div>

<script>
$(Catool.script('collection-edit'));
</script>
