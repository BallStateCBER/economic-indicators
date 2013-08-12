<h1>
	Add Dataset
</h1>

<?php echo $this->Form->create('Dataset');?>
<?php echo $this->Form->input('code'); ?>
<?php echo $this->Html->script('admin', array('inline' => false)); ?>
<?php $this->Js->buffer("dataset_form.setup();"); ?>
<?php 
	$loc_type_select = '<select id="loc_type_selection">';
	foreach ($locations as $loc_type => $l) {
		$loc_type_select .= '<option value="'.$loc_type.'">';
		switch ($loc_type) {
			case 'msa':
				$loc_type_select .=  'MSA';
				break;
			case 'country':
				$loc_type_select .=  'USA';
				break;
			default:
				$loc_type_select .=  ucwords($loc_type);	
		}
		$loc_type_select .= '</option>';
	}
	$loc_type_select .= '</select>';
	echo $this->Form->input('location_id', array(
		'label' => 'Location',
		'empty' => false,
		'options' => $locations,
		'between' => $loc_type_select
	));
?>

<div id="category_selects">
	<?php
		foreach ($categories as $loc_type => $loc_type_options) {
			$frequencies = array_keys($loc_type_options);
			$categories_list = array();
			foreach ($loc_type_options as $frequency => $categories_by_frequency) {
				foreach ($categories_by_frequency as $category) {
					$categories_list[$frequency][$category['id']] = $category['name'];
				}
			}
			$frequency_select = $this->Form->input('frequency', array(
				'options' => array_combine($frequencies, $frequencies),
				'id' => 'frequency_select_'.$loc_type,
				'div' => false,
				'label' => false,
				'class' => 'frequency'
			));
			echo $this->Form->input('category_id', array(
				'label' => 'Category',
				'empty' => false,
				'options' => $categories_list,
				'between' => $frequency_select,
				'div' => array('id' => 'categories_'.$loc_type),
				'disabled' => true
			));
		}
	?>
</div>

<?php echo $this->Form->end(__('Submit'));?>

<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Datasets'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Categories'), array('controller' => 'categories', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Category'), array('controller' => 'categories', 'action' => 'add')); ?> </li>
	</ul>
</div>