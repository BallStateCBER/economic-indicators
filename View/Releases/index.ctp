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
				<?php /*
					<thead>
						<tr>
							<th>Group</th>
							<th>Category</th>
							<th>
								Dates
								<?php if ($logged_in): ?>
									<span class="footnote">
										(click to edit)
									</span>
								<?php endif; ?>
							</th>
						</tr>
					</thead>
				*/ ?>
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
										<?php foreach ($dates as $date => $release_id): ?>
											<li>
												<?php
													$timestamp = strtotime($date);
													$upcoming = $timestamp >= time();
													$pattern = date('Y', $timestamp) == date('Y') ? 'M j' : 'M j, Y';
													$displayed_date = date($pattern, $timestamp);
												 	if ($logged_in) {
												 		echo $this->Html->link($displayed_date, array(
												 			'controller' => 'releases',
												 			'action' => 'edit',
												 			$release_id
												 		));
												 	} elseif ($upcoming) {
												 		echo '<span class="upcoming">'.$displayed_date.'</span>';
													} else {
												 		echo $displayed_date;
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