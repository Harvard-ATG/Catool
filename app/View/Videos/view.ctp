<?php echo $this->element('breadcrumbs', array('crumbs' => array(
	array(__('Home'), '/'),
	array(__('My Collections'), '/collections'),
	array($this->Text->truncate($collection['Collection']['display_name'], 25), array('controller' => 'collections', 'action' => 'view', $target['Video']['collection_id'])), 
	array($this->Text->truncate($target['Video']['display_name'], 25), $this->Html->request->here(false))
))); ?>

<?php
$infoElements = array(
	'Title' => @$target['Video']['display_name'],
	'Creator' => @$target['Video']['display_creator'],
	'Date' => @$target['Video']['display_date'],
	'Description' => @$target['Video']['display_description']
);
?>

<div class="row gutter">
	<div class="span12">
		<div class="pull-left">
			<div class="notes-btn-group">
				<?php 
				if(isset($neighbors['prev'])) {
					echo $this->Html->link('&lt;', array(
							'controller' => $this->TargetLink->controllerName($neighbors['prev']['type']),
							'action' => 'view',
							$neighbors['prev']['id']), 
						array('class' => 'btn', 'escape' => false));
				}

				echo $this->Html->link('Menu', array(
						'controller' => 'collections', 
						'action' => 'view', 
						$target['Video']['collection_id']),
					array('class' => 'btn upper')
				);

				if(isset($neighbors['next'])) {
					echo $this->Html->link('&gt;', array(
							'controller' => $this->TargetLink->controllerName($neighbors['next']['type']),
							'action' => 'view',
							$neighbors['next']['id']),
						array('class' => 'btn', 'escape' => false)); 
				}
				?>
			</div>
		</div>
		<div class="pull-left" style="margin-left: 1em">
				<h3 class="upper"><?php echo $target['Video']['display_name']; ?></h3>
		</div>
	</div>
</div>

<div class="row">
	<div class="span4">
		<div class="row gutter-medium">
			<div class="span4">
				<div class="notes-video-player">
					<video id="notes-video-player-1" width="100%" height="240" controls="controls" preload="auto" class="video-js vjs-default-skin">
					    <source type="<?php echo $target['Resource']['file_type']; ?>" src="<?php echo $target['Resource']['url']; ?>" />

					    <!-- MP4 for Safari, IE9, iPhone, iPad, Android, and Windows Phone 7 -->
					    <!--source type="video/mp4" src="myvideo.mp4" /-->
							
					    <!-- WebM/VP8 for Firefox4, Opera, and Chrome -->
					    <!--source type="video/webm" src="myvideo.webm" /-->
					    
					    <!-- Ogg/Vorbis for older Firefox and Opera versions -->
					    <!--source type="video/ogg" src="myvideo.ogv" /-->
					    
					    <!-- Youtube -->
					    <!--source type="video/youtube" src="http://www.youtube.com/watch?v=4wvpdBnfiZo"/-->
					</video>
				</div>
			</div>
		</div>
		<div class="row visible-desktop visible-tablet">
			<div class="span4">
				<?php echo $this->element('target_info', array('info' => $infoElements)); ?>
			</div>
		</div>
	</div>
	
	<div class="span8">
		
		<!-- TABS -->
		<ul class="notes-tabs nav nav-tabs">
			<?php if($current_user['isAdmin'] || !$target['TargetSetting']['lock_annotations']): ?>
			<li>
				<a id="new_comment_tab" href="javascript:;" data-toggle="tab" data-target=".note-form-view" class="offset">New Comment</a>
			</li>
			<?php endif; ?>
			<li class="active">
				<a id="current_comments_tab" href="javascript:;" data-toggle="tab" data-target=".notes-view">Current Comments</a>
			</li>
			<li class="visible-phone">
				<a id="target_info_tab" href="javascript:;" data-toggle="tab" data-target=".notes-info-view" class="info">&nbsp;</a>
			</li>
		</ul>
		
		<!-- TAB CONTENT -->
		<div class="tab-content">
			<!-- NOTES -->
			<div class="tab-pane active notes-view">
                <form class="well form-search">
                    <input type="text" name="search" class="input-medium search-query">
                    <button type="submit" class="btn">
                        <i class="icon-search"></i> Search
                    </button>
                </form>
				<div style="padding: 5px 0;">
					<a href="#" class="note-icon-expand notes-toggle" rel="tooltip" title="Expand/Collapse Comments"></a>
					<a href="#" class="note-icon-sync notes-sync" rel="tooltip" title="Sync/Unsync Comments"></a>
					<a href="#" class="note-icon-refresh notes-refresh" rel="tooltip" title="Refresh Comments"></a>
	                <div class="notes-sort notes-btn-group pull-right">
	                	<span class="sort-label">Sort by:</span>
	                    <a class="btn" href="#" data-sort="title">Title</a>
	                    <a class="btn" href="#" data-sort="date">Date</a>
	                    <a class="btn" href="#" data-sort="start-time">Start</a>
	                </div>		
				</div>
				<div class="notes note-annotations"></div>
			</div>
			
			<!-- NEW NOTE -->
			<div class="tab-pane note-form-view">
				<div class="alert-messages"></div>
				<form class="note-form form-horizontal" action="/notes" method="post">
					<fieldset>
						<div class="control-group">
							<label class="control-label">Start Time</label>
							<div class="controls">
								<input type="text" name="start_time" class="pull-left input-mini" style="margin-right: 1em" />
								<p class="help-block"><small>The start time of the video segment you are referencing.</small></p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">End Time</label>
							<div class="controls">
								<input type="text" name="end_time" class="pull-left input-mini" style="margin-right: 1em" />
								<p class="help-block"><small>The end time of the video segment you are referencing.</small></p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Title</label>
							<div class="controls">
								<input type="text" class="input-xlarge" name="title" />
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Comment</label>
							<div class="controls">
								<textarea class="input-xlarge" rows="3" name="body"></textarea>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Tags</label>
							<div class="controls">
								<input type="text" class="input-xlarge" name="tags" />
								<p class="help-block"><small>Comma separated list of tags (max: 10)</small></p>
							</div>
						</div>
						<div class="form-actions">
							<button type="submit" class="btn btn-primary">Save Comment</button>
							<button class="btn cancel">Cancel</button>
						</div>
					</fieldset>
				</form>
			</div>
			<div class="tab-pane notes-info-view">
				<?php echo $this->element('target_info', array('info' => $infoElements, 'header' => 'Video Information')); ?>
			</div>
		</div>
	</div>
</div>

<hr/>

<?php 
$viewOptions = array(
	'data' => array(
		'notes' => $notes,
		'video' => array(
			'id' => $target['Video']['id'],
			'url' => $target['Resource']['url'],
			'duration' => $target['Resource']['duration'],
			'type' => $target['Resource']['file_type']	
		)
	),
	'config' => array(
		'noteId' => $note_id,
		'sortNotesBy' => array('key' => 'start-time', 'dir' => 'asc'),
		'syncAnnotations' => !empty($target['TargetSetting']['sync_annotations']),
		'lockAnnotations' => !empty($target['TargetSetting']['lock_annotations']),
		'lockComments' => !empty($target['TargetSetting']['lock_comments']),
		'highlightAdmins' => !empty($target['TargetSetting']['highlight_admins']) ?  $admin_user_ids : false
	)
);
?>

<script>

$(function(){
	Catool.baseUrl = <?php echo json_encode($this->Html->url('/')); ?>;
	Catool.user.set(<?php echo json_encode($current_user); ?>);

	new Catool.views.VideoAppView({
		el: $('.catool').first(),
		data: <?php echo json_encode($viewOptions['data']); ?>,
		config: <?php echo json_encode($viewOptions['config']); ?>
	});
});
</script>

