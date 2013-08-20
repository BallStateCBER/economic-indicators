<input id="sidebar_category_select" class="select2-offscreen" type="hidden" tabindex="-1">

<?php
	$this->Html->css('/select2/select2.css', null, array('inline' => false));
	$this->Html->script('/select2/select2.js', array('inline' => false));
	$dataset_view_url = Router::url(array(
		'controller' => 'datasets',
		'action' => 'view'
	), true);
	$this->Js->buffer("
		prepareSidebar();
		indicatorsSearch.results = ".$this->Js->object($select2_categories).";
		indicatorsSearch.datasetViewUrl = '".$dataset_view_url."';
		indicatorsSearch.locationTypes = ".$this->Js->object($select2_location_types).";
		indicatorsSearch.prepareSelect2();
	");
?>

<?php foreach ($menu as $k => $section): ?>
	<section>
		<h2>
			<?php
				$location_name = $section['LocationType']['display_name'];
				echo ($location_name == 'US') ? 'United States' : $location_name;
			?>
		</h2>

		<?php if (empty($section['Category'])): ?>
			<ul class="menu_section">
				<li class="no_data">
					No data available
				</li>
			</ul>
		<?php else: ?>
			<ul class="menu_section">
				<?php foreach ($section['Category'] as $group => $categories): ?>
					<li>
						<?php echo $this->Html->link($group,
							array(
								'controller' => 'category_groups',
								'action' => 'view',
								'category_group_id' => array_search($group, $category_groups),
								'location_type_id' => $section['LocationType']['id']
							),
							array('class' => 'group')
						); ?>
						<?php if ($location_name == 'Indiana Counties' || $location_name == 'Indiana Metropolitan Statistical Areas (MSAs)'): ?>
							<div class="categories_in_group" style="display: block;">
						<?php else: ?>
							<div class="categories_in_group">
						<?php endif; ?>
							<ul>
								<?php foreach ($categories as $category): ?>
									<li>
										<?php
											$label = '<span class="category">'.$category['name'].'</span>';
											$label .= '<span class="frequency">';
											$label .= strtolower(reset(explode(' ', $category['Frequency']['name'])));
											$label .= '</span>';
											echo $this->Html->link(
												$label,
												array(
													'controller' => 'datasets',
													'action' => 'view',
													$category['id']
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
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</section>
<?php endforeach; ?>