<?php
//	pr($frequencies);
//	pr($location_type);
//	pr($category_group);
?>
<div class="categories_in_group">
	<h1 class="page_title">
		<?php echo $location_type['LocationType']['display_name']; ?>
		-
		<?php echo $category_group['CategoryGroup']['name']; ?>
	</h1>
	<ul>
		<?php foreach ($category_group['Category'] as $category): ?>
			<li>
				<?php
					$label = '<span class="category">'.$category['name'].'</span>';
					$label .= '<span class="frequency">';
					$frequency_words = explode(' ', $frequencies[$category['frequency_id']]);
					$frequency_word = reset($frequency_words);
					$label .= strtolower($frequency_word);
					$label .= '</span>';
					echo $this->Html->link(
						$label,
						array(
							'controller' => 'datasets',
							'action' => 'view',
							'category_id' => $category['id']
						),
						array(
							'escape' => false,
							'class' => 'sidebar_category'
						)
					);
				?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>