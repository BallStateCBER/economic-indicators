<div class="releases view">
<h2><?php  echo __('Release');?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($release['Release']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Dataset'); ?></dt>
		<dd>
			<?php echo $this->Html->link($release['Dataset']['id'], array('controller' => 'datasets', 'action' => 'view', $release['Dataset']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date'); ?></dt>
		<dd>
			<?php echo h($release['Release']['date']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Release'), array('action' => 'edit', $release['Release']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Release'), array('action' => 'delete', $release['Release']['id']), null, __('Are you sure you want to delete # %s?', $release['Release']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Releases'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Release'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Datasets'), array('controller' => 'datasets', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Dataset'), array('controller' => 'datasets', 'action' => 'add')); ?> </li>
	</ul>
</div>
