<?php if(!empty($crumbs)): ?>
	<ul class="breadcrumb">
	<?php $numCrumbs = count($crumbs); ?>
	<?php for($crumbIdx = 0; $crumbIdx < $numCrumbs; $crumbIdx++):  ?>
		<?php if($crumbIdx === $numCrumbs -1): ?>
			<li class="active">
				<?php echo $this->Html->link($crumbs[$crumbIdx][0], $crumbs[$crumbIdx][1]); ?>
			</li>
		<?php else: ?>
			<li>
				<?php echo $this->Html->link($crumbs[$crumbIdx][0], $crumbs[$crumbIdx][1]); ?>
				<span class="divider">/</span>
			</li>
		<?php endif; ?>
	<?php endfor; ?>
	</ul>
<?php endif; ?>
