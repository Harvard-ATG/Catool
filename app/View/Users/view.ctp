<div class="row">
	<div class="span9">
		<div class="page-header">
			<h1><?php  echo __('User');?></h1>
		</div>
		<dl>
			<dt><?php echo __('Id'); ?></dt>
			<dd>
				<?php echo h($user['User']['id']); ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Name'); ?></dt>
			<dd>
				<?php echo h($user['User']['name']); ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Email'); ?></dt>
			<dd>
				<?php echo isset($user['User']['email']) ? h($user['User']['email']) : 'n/a'; ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Created'); ?></dt>
			<dd>
				<?php echo h($this->Time->nice($user['User']['created'])); ?>
				&nbsp;
			</dd>
		</dl>
	</div>
</div>
