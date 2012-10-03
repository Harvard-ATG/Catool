<?php 
echo $this->NavRenderer->primary(array(
	array('name' => 'Collections', 'url' => '/collections'),
), array('class' => 'nav')); 

echo $this->NavRenderer->adminOnly(array(
	array('name' => 'Admin', 'url' => '/admin/sites'),
), array('class' => 'nav')); 

?>

<?php if($this->Session->read('Auth.User')): ?>
	<ul class="nav pull-right">
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="icon-user icon-white"></i>
				<?php echo $this->NavRenderer->getUserName($this->Session->read('Auth.User')); ?>
				<b class="caret"></b>
			</a>
			<ul class="dropdown-menu">
				<li>
					<a href="<?php echo $this->Html->url(array(
						'controller' => 'users', 
						'action' => 'view', 
						'admin' => false,
						$this->Session->read('Auth.User.id'))); 
					?>">
					Profile
					</a>
				</li>
				<?php $userMenuItems = $this->NavRenderer->getUserMenuItems(); ?>
				<?php foreach($userMenuItems as $item): ?>
					<li><?php echo $item; ?></li>
				<?php endforeach; ?>
			</ul>			
		</li>
	</ul><!--/.nav-collapse -->
<?php endif; ?>
