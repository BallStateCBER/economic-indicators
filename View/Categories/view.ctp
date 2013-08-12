<h1>
	<?php echo $title_for_layout; ?>
</h1>

Select a location:
<ul>
	<?php foreach ($locations as $location_id => $location_name): ?>
		<li>
			<?php echo $this->Html->link($location_name.$loc_name_postfix, array(
				'controller' => 'datasets',
				'action' => 'view',
				'category_id' => $category_id,
				'location_id' => $location_id
			)); ?>
		</li>
	<?php endforeach; ?>
</ul>