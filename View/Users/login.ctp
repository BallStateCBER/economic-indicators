<h1 class="page_title">
	Log in
</h1>
<?php 
	echo $this->Form->create('User', array('action' => 'login'));
	echo $this->Form->input('email');
	echo $this->Form->input('password');
	echo $this->Form->input('auto_login', array(
		'type' => 'checkbox', 
		'label' => array('text' => ' Log me in automatically', 'style' => 'display: inline;'),
		'checked' => true
	)); 
	echo $this->Form->end('Login');