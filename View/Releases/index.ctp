<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>

<div id="upcoming_releases">
	<?php if (empty($structure)): ?>
		<p>
			There are currently no upcoming data releases scheduled. Please check back later for updates.
		</p>
	<?php else: ?>
		<?php foreach ($structure as $loc_type_id => $cat_groups): ?>
			<h2>
				<?php echo $location_types[$loc_type_id]; ?>
			</h2>
			<table>
				<tbody>
					<?php foreach ($cat_groups as $cat_group_id => $cats): ?>
						<tr>
							<td rowspan="<?php echo count($cats); ?>">
								<?php echo $category_groups[$cat_group_id]; ?>
							</td>
							<?php
								$k = 0;
								foreach ($cats as $cat_id => $dates):
							?>
								<td>
									<?php
										$category_name = $categories[$cat_id];
										$category_name = str_replace(' By ', ' by ', $category_name);
										echo $this->Html->link(
											$category_name,
											array(
												'controller' => 'categories',
												'action' => 'view',
												$cat_id
											)
										);
									?>
								</td>
								<td>
									<ul>
										<?php
											$dates_list = array_keys($dates);
											$displayed_count = 0;
											foreach ($dates_list as $i => $date):

												// Don't display more than four dates per category
												if ($displayed_count == 4) {
													break;
												}
										?>
											<li>
												<?php
													$release_id = $dates[$date];
													$timestamp = strtotime($date);
													$upcoming = $timestamp >= time();
													$most_recent = $timestamp < time() && (! isset($dates_list[$i + 1]) || strtotime($dates_list[$i + 1]) >= time());
													$pattern = date('Y', $timestamp) == date('Y') ? 'M j' : 'M j, Y';
													$displayed_date = date($pattern, $timestamp);
												 	if (! $upcoming && ! $most_recent) {
												 		// Don't show
												 	} elseif ($logged_in) {
												 		echo $this->Html->link($displayed_date, array(
												 			'controller' => 'releases',
												 			'action' => 'edit',
												 			$release_id
												 		));
														$displayed_count++;
												 	} else {
												 		if ($most_recent) {
													 		echo '<strong>'.$displayed_date.'</strong>';
														} else {
													 		echo $displayed_date;
														}
														$displayed_count++;
												 	}
												?>
											</li>
										<?php endforeach; ?>
									</ul>
								</td>
							<?php
								$k++;
								if ($k < count($cats)) {
									echo '</tr><tr>';
								}
								endforeach;
							?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endforeach; ?>
	<?php endif; ?>
</div>