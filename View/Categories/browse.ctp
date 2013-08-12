<h1>
	<?php echo $title_for_layout; ?>
</h1>

<ul>
	<?php foreach ($categories as $category): ?>
		<li>
			<?php echo $this->Html->link($category['Category']['name'], array(
				'controller' => 'datasets',
				'action' => 'view',
				$category['Category']['id']
			)); ?>
		</li>
	<?php endforeach; ?>
</ul>

<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
</div>