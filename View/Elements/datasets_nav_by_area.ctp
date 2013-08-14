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

						<div class="categories_in_group">
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

<?php $this->Js->buffer("prepareSidebar();"); ?>