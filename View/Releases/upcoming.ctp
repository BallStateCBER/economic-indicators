<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>

<?php foreach ($calendar as $date => $releases): ?>
	<?php if ($date == 'max_date') continue; ?>
	<h2>
		<?php echo date('F j, Y', strtotime($date)); ?>
	</h2>
	<ul>
		<?php foreach ($releases as $release): ?>
			<li>
				<?php echo $release['category']; ?>
				<br />
				<?php echo $release['location_type_name']; ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endforeach; ?>
