<?php
App::uses('AppModel', 'Model');
class CategoryGroup extends AppModel {
	public $name = 'CategoryGroup';
	public $hasMany = array('Category');
	
	
}