
<div class="page-header">
	<h1>Install Wizard</h1>
</div>

<div class="row">
	<div class="span12">

		<div class="alert <?php echo $db_status['connected'] ? 'alert-success' : 'alert-error'; ?> ">
			<?php if($db_status['connected']): ?>
				<?php echo __('Database Connection OK'); ?>
			<?php else: ?>
				<strong><?php echo __('Database Connection Error: '); ?></strong>
				<?php echo h($db_status['error']); ?>
			<?php endif; ?>
		</div>

		<form id="database" method="post" action="<?php echo $this->Html->url('/installs/database'); ?>">	
		<fieldset>
			<legend>Database Configuration</legend>

			<div class="control-group">
				<label class="control-label" for="host">Database</label>
				<div class="controls">
					<select name="data[DATABASE_CONFIG][datasource]">
						<option value="Mysql" <?php echo $db->default['datasource'] == 'Database/Mysql' ? 'selected' : ''; ?>>Mysql</option>
						<option value="Sqlite" <?php echo $db->default['datasource'] == 'Database/Sqlite' ? 'selected' : ''; ?>>Sqlite</option>
						<option value="Postgres" <?php echo $db->default['datasource'] == 'Database/Postgres' ? 'selected' : ''; ?>>Postgres</option>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="host">Host</label>
				<div class="controls">
					<input type="text" id="host" name="data[DATABASE_CONFIG][host]" value="<?php echo $db->default['host']; ?>" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="port">Port (optional)</label>
				<div class="controls">
					<input type="text" id="port" name="data[DATABASE_CONFIG][port]" value="<?php echo $db->default['port']; ?>" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="login">User</label>
				<div class="controls">
					<input type="text" id="login" name="data[DATABASE_CONFIG][login]" value="<?php echo $db->default['login']; ?>" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="password">Password</label>
				<div class="controls">
					<input type="text" id="password" name="data[DATABASE_CONFIG][password]" value="<?php echo $db->default['password']; ?>" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="database">Database Name</label>
				<div class="controls">
					<input type="text" id="database" name="data[DATABASE_CONFIG][database]" value="<?php echo $db->default['database']; ?>" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="prefix">Table Prefix (optional)</label>
				<div class="controls">
					<input type="text" id="prefix" name="data[DATABASE_CONFIG][prefix]" value="<?php echo $db->default['prefix']; ?>" />
				</div>
			</div>
		</fieldset>

		<div class="form-actions">
			<input type="submit" name="submit" value="<?php echo __('Save Configuration'); ?>" class="btn btn-primary" />
		</div>
	</div>
</div>
