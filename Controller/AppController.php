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
		'DebugKit.Toolbar',
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

		$this->__prepareSidebar();
	}

	private function __prepareSidebar() {
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
		$this->loadModel('Category');
		foreach ($menu as $k => &$location_type) {
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

		$this->loadModel('Frequency');
		$this->loadModel('CategoryGroup');
		$this->set(array(
			'menu' => $menu,
			'logged_in' => $this->Auth->loggedIn(),

			'category_groups' => $this->CategoryGroup->find('list'),
			'frequencies' => $this->Frequency->find('list')
		));
	}
}