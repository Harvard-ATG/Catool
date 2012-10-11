<form action="<?php echo $action_url; ?>" method="post"	class="form-horizontal">
	<fieldset>
		<legend><?php echo __('Information') ?></legend>
		
		<input type="hidden" name="data[Video][id]" value="<?php echo @$video['Video']['id']; ?>">
		<input type="hidden" name="data[Video][collection_id]" value="<?php echo $collection_id; ?>">
		<input type="hidden" name="data[Resource][id]" value="<?php echo @$video['Resource']['id']; ?>">
		<input type="hidden" name="data[TargetSetting][id]" value="<?php echo @$video['TargetSetting']['id']; ?>">
		<input type="hidden" id="resource_duration" name="data[Resource][duration]" value="<?php echo @$video['Resource']['duration']; ?>">

		<div id="resource-url-control-group" class="control-group <?php echo $this->Form->isFieldError('Resource.duration') ? 'error' : ''; ?>">
			<label class="control-label" for="resource_url"><?php echo __('Enter URL to video'); ?></label>
			<div class="controls">
				<div class="input-append">
					<input type="text" id="resource_url" name="data[Resource][url]" value="<?php echo @$video['Resource']['url']; ?>">
					<select id="resource_file_type" name="data[Resource][file_type]" style="width:auto">
						<option value="">Video Format</option>
						<option value="video/mp4" <?php echo @$video['Resource']['file_type'] === 'video/mp4' ? 'selected' : '' ?>>MP4</option>
						<option value="video/flv" <?php echo @$video['Resource']['file_type'] === 'video/flv' ? 'selected' : '' ?>>FLV</option>
						<option value="video/youtube" <?php echo @$video['Resource']['file_type'] === 'video/youtube' ? 'selected' : '' ?>>Youtube</option>
					</select>
					<button id="validate_video" class="btn" type="button"><?php echo __('Validate'); ?></button>
				</div>
			
				<?php if($this->Form->isFieldError('Resource.duration')): ?>
					<span class="help-inline"><?php echo __('Invalid video URL'); ?></span>
				<?php endif; ?>			
				
				<span class="help-block">Format must be one of: MP4, FLV, or <a href="http://support.google.com/youtube/bin/answer.py?hl=en&answer=57741">Youtube</a>.</span>
			
				<div id="video-player-container-1" class="span4" style="<?php echo !isset($video['Resource']['url']) || empty($video['Resource']['duration']) ? 'display:none;' : ''; ?> margin-left: 0">
					<!--  instance of video player here -->
					<video id="video-player-1" width="100%" height="240" controls="controls" preload="auto" class="video-js vjs-default-skin">
						<?php if(isset($video['Resource']) && isset($video['Resource']['url']) && !empty($video['Resource']['duration'])): ?>
							<source type="<?php echo @$video['Resource']['file_type']; ?>" src="<?php echo @$video['Resource']['url']; ?>"/>
						<?php endif; ?>
					</video>
				</div>
			</div>
		</div>
	</fieldset>

		<div class="control-group <?php echo $this->Form->isFieldError('Video.display_name') ? 'error' : ''; ?>">
			<label class="control-label" for="display_name"><?php echo __('Title'); ?></label>
			<div class="controls">
				<input type="text" id="display_name" name="data[Video][display_name]" value="<?php echo @$video['Video']['display_name']; ?>">
				<?php if($this->Form->isFieldError('Video.display_name')): ?>
					<span class="help-inline"><?php echo $this->Form->error('Video.display_name'); ?></span>
				<?php endif; ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="display_name"><?php echo __('Artist'); ?></label>
			<div class="controls">
				<input type="text" id="display_name" name="data[Video][display_creator]" value="<?php echo @$video['Video']['display_creator']; ?>">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="display_created"><?php echo __('Creation Year'); ?></label>
			<div class="controls">
				<input type="text" id="display_created" name="data[Video][display_created]" value="<?php echo @$video['Video']['display_created']; ?>">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="display_description"><?php echo __('Description'); ?></label>
			<div class="controls">
				<textarea name="data[Video][display_description]"><?php echo @$video['Video']['display_description']; ?></textarea>
			</div>
		</div>

	<fieldset>
		<legend><?php echo __('Settings') ?></legend>
		
		<div class="control-group">
			<label class="control-label" for="visibility"><?php echo __('Visibility'); ?></label>
			<div class="controls">
				<label class="radio inline">
					<input type="radio" name="data[Video][hidden]" value="0" <?php echo @$video['Video']['hidden'] ? '' : 'checked="checked"'; ?>>
					<?php echo __('Visible'); ?>
				</label>
				<label class="radio inline">
					<input type="radio" name="data[Video][hidden]" value="1" <?php echo @$video['Video']['hidden'] ? 'checked="checked"' : ''; ?>>
					<?php echo __('Hidden'); ?>
				</label>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="visibility"><?php echo __('Annotations'); ?></label>
			<div class="controls">
				<label class="checkbox">
					<input type="hidden" name="data[TargetSetting][lock_annotations]" value="1">
					<input type="checkbox" name="data[TargetSetting][lock_annotations]" value="0" <?php echo @$video['TargetSetting']['lock_annotations'] ? '' : 'checked="checked"'; ?>>
					<?php echo __('Allow public annotations'); ?>
				</label>
				<label class="checkbox">
					<input type="hidden" name="data[TargetSetting][lock_comments]" value="1">
					<input type="checkbox" name="data[TargetSetting][lock_comments]" value="0" <?php echo @$video['TargetSetting']['lock_comments'] ? '' : 'checked="checked"'; ?>>
					<?php echo __('Allow comments on annotations'); ?>
				</label>
				<label class="checkbox">
					<input type="hidden" name="data[TargetSetting][sync_annotations]" value="0">
					<input type="checkbox" name="data[TargetSetting][sync_annotations]" value="1" <?php echo @$video['TargetSetting']['sync_annotations'] ? 'checked="checked"' : ''; ?>>
					<?php echo __('Synchronize annotations to video by default'); ?>
				</label>
				<label class="checkbox">
					<input type="hidden" name="data[TargetSetting][highlight_admins]" value="0">
					<input type="checkbox" name="data[TargetSetting][highlight_admins]" value="1" <?php echo @$video['TargetSetting']['highlight_admins'] ? 'checked="checked"' : ''; ?>>
					<?php echo __('Highlight'); ?>
						<span class="role-legend role-admin"><?php echo __('administrator'); ?></span>
					<?php echo __('annotations and comments'); ?>
				</label>
			</div>
		</div>
	
		<div class="form-actions">
			<?php if(!empty($video['Video']['id'])): ?>
				<span class="pull-right">
					<button id="delete_video_button" class="btn btn-danger">Delete</button>
				</span>
			<?php endif; ?>
			<button type="submit" class="btn btn-primary">Save Changes</button>
			<?php echo $this->Html->link('Cancel', array('controller' => 'collections', 'action' => 'edit_items', $collection_id), array('class' => 'btn')); ?>
		</div>
	</fieldset>
</form>


<div id="playerErrorModal" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h3><?php echo __('Video Validation Failed'); ?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo __('The video could not be validated. Please check the URL and try again.'); ?></p>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal"><?php echo __('Close'); ?></button>
	</div>
</div>

<div id="videoDeleteModal" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h3>Delete Video</h3>
	</div>
	<div class="modal-body">
		<p>Are you sure you want to permanently delete <b><?php echo $video['Video']['display_name']; ?></b>?</p>
	</div>
	<div class="modal-footer">
		<?php echo $this->Form->postLink(__('Yes, Permanently Delete'), array('controller' => 'videos', 'action' => 'delete', 'admin' => true, '?' => array('collection_id' => $video['Collection']['id']), $video['Video']['id']), array('class' => 'btn btn-danger')); ?>						
		<button class="btn" data-dismiss="modal"><?php echo __('Cancel'); ?></button>
	</div>	
</div>


<script>
$(Catool.script('video-edit'));
</script>
