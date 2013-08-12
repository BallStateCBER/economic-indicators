<h1>
	Add Category
</h1>

<div class="categories form">
	<?php echo $this->Form->create('Category');?>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('frequency_id');
		echo $this->Form->input('location_type_id');
	?>
	<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Categories'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Datasets'), array('controller' => 'datasets', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Dataset'), array('controller' => 'datasets', 'action' => 'add')); ?> </li>
	</ul>
</div>
