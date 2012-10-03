<?php echo $this->element('collections_admin_edit_header'); ?>

<div class="row">
	<div class="span12">
		<div id="collection_settings_form">
		<?php echo $this->Form->create('Collection');?>
			<fieldset>
				<?php
					echo $this->Form->input('id');
					echo $this->Form->input('display_name');
					echo $this->Form->label('display_description');
					echo $this->Form->textarea('display_description');
				?>
			</fieldset>
			<div class="form-actions">
				<span class="pull-right">
					<button id="delete_collection_button" class="btn btn-danger"><?php echo __('Delete'); ?></button>
				</span>
				<?php echo $this->Form->button('Save Changes', array('class' => 'btn btn-primary')); ?>
			</div>
		<?php echo $this->Form->end(); ?>
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
