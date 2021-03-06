<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>

<?php foreach ($releases as $date => $releases_on_date): ?>
	<h2>
		<?php echo date('F j, Y', strtotime($date)); ?>
	</h2>
	<ul>
		<?php foreach ($releases_on_date as $release): ?>
			<li>
				<?php
					switch ($release['location_type_name']) {
						case 'United States':
							echo 'U.S.';
							break;
						default:
							echo $release['location_type_name'];
					}
				?>
				<?php echo $release['category']; ?>,
				<?php echo $release['frequency']; ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endforeach; ?>
