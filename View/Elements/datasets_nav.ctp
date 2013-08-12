<?php foreach ($menu as $section): ?>
	<h2>
		<?php echo $section['Frequency']['name']; ?>
	</h2>
	
	<ul class="menu_section">
		<?php foreach ($section['LocationType'] as $loc_type): ?>
			<li>
				<a href="#">
					<?php echo $loc_type['display_name']; ?>
				</a>
				<ul style="display: none;">
					<?php foreach ($loc_type['Category'] as $category_name => $category): ?>
						<li>
							<?php echo $this->Html->link($category_name, array(
								'controller' => 'categories', 
								'action' => 'view', 
								$category['id']
							)); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endforeach; ?>

<?php $this->Js->buffer("
	$('ul.menu_section > li > a').click(function(event) {
		event.preventDefault();
		var submenu_parent = $(this).parent('li');
		var submenu = $(this).next('ul');
		if (submenu.is(':visible')) {
			submenu.slideUp(300);
			submenu_parent.removeClass('open');
		} else {
			$('ul.menu_section > li.open').removeClass('open').children('ul').slideUp(300);
			submenu.slideDown(300);
			submenu_parent.addClass('open');
		}
	});
"); ?>