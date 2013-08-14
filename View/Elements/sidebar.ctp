<section>
	<?php echo $this->Html->link(
		'Economic Indicators Home',
		array(
			'controller' => 'pages',
			'action' => 'home'
		)
	); ?>
</section>

<?php echo $this->element('datasets_nav_by_area'); ?>

<?php if ($logged_in): ?>
	<section>
		<h2>User</h2>
		<ul class="unstyled">
			<li>
				<?php echo $this->Html->link('My Account', array('controller' => 'users', 'action' => 'my_account')); ?>
			</li>
			<li>
				<?php echo $this->Html->link('Log out', array('controller' => 'users', 'action' => 'logout')); ?>
			</li>
		</ul>
	</section>

	<section>
		<h2 class="admin">Admin</h2>
		<ul class="unstyled">
			<li>
				<?php echo $this->Html->link('Add dataset', array('controller' => 'datasets', 'action' => 'add')); ?>
			</li>
			<li>
				<?php echo $this->Html->link('Add release', array('controller' => 'releases', 'action' => 'add')); ?>
			</li>
			<li>
				<?php echo $this->Html->link('List releases', array('controller' => 'releases', 'action' => 'index')); ?>
			</li>
			<li>
				<?php echo $this->Html->link('Add user', array('controller' => 'users', 'action' => 'add')); ?>
			</li>
			<li>
				<?php echo $this->Html->link('Clear cache', array('controller' => 'pages', 'action' => 'clear_cache')); ?>
			</li>
		</ul>
	</section>
<?php else: ?>
	<section>
		<?php echo $this->Html->link('Admin log in', array('controller' => 'users', 'action' => 'login'), array('class' => 'login')); ?>
	</section>
<?php endif; ?>