<div class="datasets index">
	<h2><?php echo __('Datasets');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
		<th><?php echo $this->Paginator->sort('location_id');?></th>
		<th><?php echo $this->Paginator->sort('category_id');?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	foreach ($datasets as $dataset): ?>
	<tr>
		<td>
			<?php //echo $this->Html->link($dataset['Location']['name'], array('controller' => 'locations', 'action' => 'view', $dataset['Location']['id'])); ?>
			<?php echo $dataset['Location']['name']; ?>
		</td>
		<td>
			<?php echo $this->Html->link($dataset['Category']['name'], array('controller' => 'categories', 'action' => 'view', $dataset['Category']['id'])); ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array(
				'action' => 'view',
				'category_id' => $dataset['Dataset']['category_id'],
				'location_id' => $dataset['Dataset']['location_id']
			)); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $dataset['Dataset']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $dataset['Dataset']['id']), null, __('Are you sure you want to delete # %s?', $dataset['Dataset']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Dataset'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Categories'), array('controller' => 'categories', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Category'), array('controller' => 'categories', 'action' => 'add')); ?> </li>
	</ul>
</div>
