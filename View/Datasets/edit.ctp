<div class="datasets form">
<?php echo $this->Form->create('Dataset');?>
	<fieldset>
		<legend><?php echo __('Edit Dataset'); ?></legend>
        <p>
            <strong>Important:</strong>
            Make sure that the HTTPS version of DataZoa URLs (instead of the HTTP version) is used in this code.
            Otherwise, this website will not be able to display the content served by DataZoa. This may require you to
            search for "http://" in the code and manually change it to "https://".
        </p>
        <?php
            echo $this->Form->input('id');
            echo $this->Form->input('code');
            echo $this->Form->input('location_id');
            echo $this->Form->input('category_id');
        ?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Dataset.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('Dataset.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Datasets'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Categories'), array('controller' => 'categories', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Category'), array('controller' => 'categories', 'action' => 'add')); ?> </li>
	</ul>
</div>
