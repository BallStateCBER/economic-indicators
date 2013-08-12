<?php
Router::connect('/', array('controller' => 'pages', 'action' => 'home'));
Router::connect('/login', array('controller' => 'users', 'action' => 'login'));
Router::connect('/logout', array('controller' => 'users', 'action' => 'logout'));
Router::connect('/clear_cache', array('controller' => 'pages', 'action' => 'clear_cache'));

// Dataset
Router::connect(
	"/dataset/:category_id", 
	array(
		'controller' => 'datasets', 
		'action' => 'view'
	),
	array(
		'category_id' => '[0-9]+',
		'pass' => array('category_id')
	)
);
Router::connect(
	"/dataset/:category_id/:location_id", 
	array(
		'controller' => 'datasets', 
		'action' => 'view'
	),
	array(
		'category_id' => '[0-9]+', 
		'location_id' => '[0-9]+', 
		'pass' => array('category_id', 'location_id')
	)
);

// Category group
Router::connect(
	"/category_group/:category_group_id/:location_type_id", 
	array(
		'controller' => 'category_groups', 
		'action' => 'view'
	),
	array(
		'category_group_id' => '[0-9]+', 
		'location_type_id' => '[0-9]+', 
		'pass' => array('category_group_id', 'location_type_id')
	)
);

CakePlugin::routes();
require CAKE . 'Config' . DS . 'routes.php';