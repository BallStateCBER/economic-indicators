<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>
<?php echo $this->Form->create('User');?>
<?php echo $this->Form->input('email', array('label' => 'Email')); ?>
<?php echo $this->Form->input('new_password', array('label' => 'Change Password', 'type' => 'password', 'autocomplete' => 'off')); ?>
<?php echo $this->Form->input('confirm_password', array('label' => 'Confirm New Password', 'type' => 'password')); ?>
<?php echo $this->Form->input('id', array('type'=>'hidden')); ?>
<?php echo $this->Form->end('Submit'); ?>