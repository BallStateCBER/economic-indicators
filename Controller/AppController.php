<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $helpers = array(
		'Js' => array('Jquery'), 
		'Form', 
		'Html', 
		'Session'
	);
	public $components = array(
		'DataCenter.AutoLogin' => array(
			'username' => 'email',
			'expires' => '+1 year'
		),
		'Auth' => array(
			'authenticate' => array(
	            'Form' => array(
	                'fields' => array('username' => 'email')
	            )
			)
        ),
		'DataCenter.Flash'
	);
	
	public function beforeFilter() {
		$this->AutoLogin->settings = array(
			// Model settings
			'model' => 'User',
			'username' => 'email',
			'password' => 'password',
	 
			// Controller settings
			'plugin' => '',
			'controller' => 'users',
			'loginAction' => 'login',
			'logoutAction' => 'logout',
	 
			// Cookie settings
			'cookieName' => 'rememberMe',
			'expires' => '+1 year',
	 
			// Process logic
			'active' => true,
			'redirect' => true,
			'requirePrompt' => true
		);
		
		$this->Auth->allow();
	}
	
	public function beforeRender() {
		/* ARRANGED BY FREQUENCY
		$this->loadModel('Frequency');
		$frequencies = $this->Frequency->find('all', array(
			'contain' => array(
				'Category' => array(
					'fields' => array('id', 'name'),
					'LocationType' => array(
						'fields' => array('id', 'name', 'display_name')
					)
				)
			)
		));
		$frequencies = array_reverse($frequencies);
		
		$menu = array();
		foreach ($frequencies as $frequency) {
			$section = array(
				'Frequency' => $frequency['Frequency']
			);
			$loc_types = array();
			foreach ($frequency['Category'] as $category) {
				$loc_type_id = $category['LocationType']['id'];
				if (! isset($loc_types[$loc_type_id])) {
					$loc_types[$loc_type_id] = $category['LocationType'];
				}
				$loc_types[$loc_type_id]['Category'][$category['name']] = array(
					'id' => $category['id'],
					'name' => $category['name']
				);
			}
			foreach ($loc_types as $loc_type_id => &$loc_type_info) {
				ksort($loc_type_info['Category']);
			}
			$section['LocationType'] = $loc_types;
			$menu[] = $section;	
		}
		*/
		
		// Create menu for sidebar
		$this->loadModel('LocationType');
		$this->loadModel('Dataset');
		$menu = $this->LocationType->find('all', array(
			'contain' => array(
				'Category' => array(
					'fields' => array('id', 'name', 'slug', 'category_group_id'),
					'order' => 'Category.name ASC',
					'Frequency' => array(
						'fields' => array('id', 'name')
					)
				)
			)
		));
		$location_type_list = array();
		$this->loadModel('Category');
		foreach ($menu as $k => &$location_type) {
			// Populate $location_type_list for the release calendar
			$loc_type_id = $location_type['LocationType']['id'];
			$loc_type_name = $location_type['LocationType']['display_name'];
			$location_type_list[$loc_type_id] = $loc_type_name;
			
			// Arrange by group
			$location_type['Category'] = $this->Category->arrangeByGroup($location_type['Category']);
			foreach ($location_type['Category'] as $group_name => $categories_in_group) {
				foreach ($categories_in_group as $category_name => $category) {
					
					// Remove links to nonexistent data sets
					$has_data = $this->Dataset->find('count', array(
						'conditions' => array('Dataset.category_id' => $category['id'])
					));
					if (! $has_data) {
						unset($location_type['Category'][$group_name][$category_name]);
					}
				}
				
				// Remove empty groups
				if (empty($location_type['Category'][$group_name])) {
					unset($location_type['Category'][$group_name]);	
				}
			}
		}
		
		// Prepare data for release calendar
		$this->loadModel('Release');
		$categories = $this->Category->find('list');
		$releases = $this->Release->getUpcoming();
		$calendar = array();
		foreach ($releases as $release) {
			// Skip if this is a release for a Category that has been deleted 
			if (! isset($release['Category']['name']) || empty($release['Category']['name'])) {
				continue;
			}
			$date = $release['Release']['date'];
			$loc_type_id = $release['Category']['location_type_id'];
			$location_type_name = $location_type_list[$loc_type_id];
			$category_id = $release['Release']['category_id'];
			$category = $categories[$category_id];
			$calendar[$date][] = compact('location_type_name', 'category');
		}
		if (empty($releases)) {
			$calendar['max_date'] = null;
		} else {
			$dates = array_keys($calendar);
			sort($dates);
			$calendar['max_date'] = end($dates);
		}
		$this->loadModel('CategoryGroup');
		$this->set(array(
			'menu' => $menu,
			'logged_in' => $this->Auth->loggedIn(),
			'calendar' => $calendar,
			'category_groups' => $this->CategoryGroup->find('list')
		));
	}
}