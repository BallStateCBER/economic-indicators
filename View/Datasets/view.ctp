<?php
	/* Prevents loading indicator from still being visible after iframe loads
	 * Usually the iframe would just obscure it, but some oddly-configured
	 * datasets don't entirely cover it. */
	$this->Js->buffer("
		$('.dataset_container iframe').load(function() {
			$(this).closest('.dataset_container').css('background-image', 'none');
		});
	");
?>
<ul class="breadcrumbs">
	<li>
		<?php echo $this->Html->link(
			$location_name.' - '.$category_group,
			array(
				'controller' => 'category_groups',
				'action' => 'view',
				'category_group_id' => $category_group_id,
				'location_type_id' => $location_type_id
			)
		); ?>
	</li>
	<li>
		<?php echo $this->Html->link(
			$category_name,
			$this->request->here
		); ?>
		<span class="frequency">
			(<?php echo $frequency; ?>)
		</span>
	</li>
</ul>

<?php
	if ($next_release || $prev_release) {
		$today_timestamp = strtotime(date('Y-m-d'));
		echo '<p class="release_notice">';
		if ($next_release) {
			echo 'This dataset is scheduled to be updated ';
			$release_timestamp = strtotime($next_release);
			if ($next_release == date('Y-m-d')) {
				echo '<strong>today</strong>';
			} else {
				echo 'on <strong>'.date('F j', $release_timestamp).'<sup>'.date('S', $release_timestamp).'</sup>';
				$release_year = date('Y', $release_timestamp);
				if ($release_year != date('Y')) {
					echo ', '.$release_year;
				}
				echo '</strong>';
				if ($next_release < date('Y-m-d', strtotime($next_release.' +10 days'))) {
					$days_away = floor(($release_timestamp - $today_timestamp)/(60*60*24));
					if ($days_away == 1) {
						echo ' (tomorrow)';
					} else {
						echo ' ('.$days_away.' days from now)';
					}
				}
			}
			echo '. ';
		}
		if ($prev_release) {
			if ($next_release && $prev_release) {
				echo 'It ';
			} else {
				echo 'This dataset ';
			}
			$release_timestamp = strtotime($prev_release);
			echo 'was last updated ';
			if ($prev_release > date('Y-m-d', strtotime('now -10 days'))) {
				$days_away = floor(($today_timestamp - $release_timestamp)/(60*60*24));
				if ($days_away == 1) {
					echo 'yesterday.';
				} else {
					echo $days_away.' days ago.';
				}
			} else {
				echo 'on '.date('F jS', $release_timestamp);
				$release_year = date('Y', $release_timestamp);
				if ($release_year != date('Y')) {
					echo ', '.$release_year;
				}
				echo '.';
			}
		}
		echo '</p>';
	}
?>

<?php if ($dataset): ?>
	<div class="dataset_container">
		<?php echo $dataset['Dataset']['code']; ?>
	</div>
<?php else: ?>
	<p class="notification_message">
		This data set is currently unavailable. Please check back at a later date.
	</p>
<?php endif; ?>