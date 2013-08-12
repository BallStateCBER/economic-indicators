<?php
	$this->Html->script('admin', array('inline' => false));
	$this->Js->buffer("release_form.setup();");
?>

<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>

<div class="form">
	<?php 
		echo $this->Form->create('Release');
		if (isset($this->data['Release']['id'])) {
			echo $this->Form->input('id');
			if (isset($this->data['Category']['id'])) {
				$this->Js->buffer("release_form.selectCategory({$this->data['Category']['id']});");
			}
		}
	?>
	
	<fieldset>
		<?php
			$loc_options = array();
			foreach ($location_types as $loc_type_id => $loc_type) {
				switch ($loc_type) {
					case 'msa':
						$loc_options[$loc_type_id] = 'MSA';
						break;
					case 'country':
						$loc_options[$loc_type_id] = 'USA';
						break;
					default:
						$loc_options[$loc_type_id] = ucwords($loc_type);	
				}
			}
			echo $this->Form->input('location_type_name', array(
				'label' => 'Location Type',
				'id' => 'loc_type_selection',
				'options' => $loc_options
			));
		?>
		
		<div id="frequency_selects">
			<?php
				foreach ($categories as $loc_type => $loc_type_options) {
					$relevant_frequencies = array_keys($loc_type_options);
					$options = array();
					foreach ($relevant_frequencies as $freq) {
						$frequency_id = array_search($freq, $frequencies);
						$options[$frequency_id] = $freq;
					}
					$location_type_id = array_search($loc_type, $location_types);
					echo $this->Form->input('frequency', array(
						'options' => $options,
						'label' => 'Frequency',
						'class' => 'frequency',
						'div' => array(
							'id' => 'frequency_select_'.$location_type_id
						)
					));
				}
			?>
		</div>
		
		<div id="category_selects">
			<?php 
				foreach ($categories as $location_type => $freqs) {
					foreach ($freqs as $freq => $f_categories) {
						$categories_list = array();
						foreach ($f_categories as $category) {
							$categories_list[$category['id']] = $category['name'];
						}
						$location_type_id = array_search($location_type, $location_types);
						$frequency_id = array_search($freq, $frequencies);
						echo $this->Form->input('category_id', array(
							'label' => "Category",
							'empty' => false,
							'options' => $categories_list,
							'div' => array(
								'id' => 'categories_'.$location_type_id.'_'.$frequency_id
							)
						));
					}
				}
			?>
		</div>
		<?php 
			echo $this->Form->input('date', array(
				'minYear' => date('Y'),
				'maxYear' => date('Y') + 2,
			));
		?>
	</fieldset>
	<?php echo $this->Form->end('Submit');?>
</div>
<div class="actions">
	<h3>
		<?php echo __('Actions'); ?>
	</h3>
	<ul>
		<li>
			<?php 
				echo $this->Html->link(
					'List Releases', 
					array('action' => 'index')
				);
			?>
		</li>
		<?php if (isset($this->data['Release']['id'])): ?>
			<li>
				<?php echo $this->Form->postLink('Delete', 
					array(
						'action' => 'delete', 
						$this->data['Release']['id']
					),
					null, 
					'Are you sure you want to delete this release?'
				); ?>
			</li>
		<?php endif; ?>
	</ul>
</div>