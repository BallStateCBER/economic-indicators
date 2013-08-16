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
			if (empty($location_type['Category'])) {
				unset($menu[$k]);
				continue;
			}

			$select2_loctype = array(
				'text' => $location_type['LocationType']['display_name'],
				'children' => array()
			);

			// Arrange by group
			$location_type['Category'] = $this->Category->arrangeByGroup($location_type['Category']);
			foreach ($location_type['Category'] as $group_name => $categories_in_group) {

				$select2_group = array(
					'text' => $group_name,
					'children' => array()
				);

				foreach ($categories_in_group as $category_name => $category) {

					// Remove links to nonexistent data sets
					$has_data = $this->Dataset->find('count', array(
						'conditions' => array('Dataset.category_id' => $category['id'])
					));
					if ($has_data) {
						$select2_group['children'][] = array(
							'id' => $category['id'],
							'text' => $category['name'].' ('.strtolower(reset(explode(' ', $category['Frequency']['name']))).')'
						);
					} else {
						unset($location_type['Category'][$group_name][$category_name]);
					}
				}

				if (! empty($select2_group['children'])) {
					$select2_loctype['children'][] = $select2_group;
				}

				// Remove empty groups
				if (empty($location_type['Category'][$group_name])) {
					unset($location_type['Category'][$group_name]);
				}
			}

			$select2_data[] = $select2_loctype;

		}

		$this->loadModel('Frequency');
		$this->loadModel('CategoryGroup');
		$this->set(array(
			'menu' => $menu,
			'logged_in' => $this->Auth->loggedIn(),
			'category_groups' => $this->CategoryGroup->find('list'),
			'frequencies' => $this->Frequency->find('list'),
			'select2_data' => $this->__getSelect2Data($menu)
		));
	}

	private function __getSelect2Data($menu) {
		$arrange_into_groups = false;
		$select2_data = array();
		foreach ($menu as $k => &$location_type) {
			$select2_loctype = array(
				'text' => $location_type['LocationType']['display_name'],
				'children' => array()
			);
			foreach ($location_type['Category'] as $group_name => $categories_in_group) {
				$select2_group = array(
					'text' => $group_name,
					'children' => array()
				);
				foreach ($categories_in_group as $category_name => $category) {
					$label = $category['name'].' ('.strtolower(reset(explode(' ', $category['Frequency']['name']))).')';
					$select2_group['children'][$label] = array(
						'id' => $category['id'],
						'text' => $label
					);
				}
				if ($arrange_into_groups) {
					// Change from associative to numerically-indexed
					$select2_group['children'] = array_values($select2_group['children']);
					$select2_loctype['children'][] = $select2_group;
				} else {
					$select2_loctype['children'] = array_merge($select2_loctype['children'], $select2_group['children']);
				}
			}
			if (! $arrange_into_groups) {
				ksort($select2_loctype['children']);
				$select2_loctype['children'] = array_values($select2_loctype['children']);
			}
			$select2_data[] = $select2_loctype;
		}
		return $select2_data;
	}

	public function __setUpcomingReleases() {
		$this->loadModel('LocationType');
		$location_type_list = $this->LocationType->find('list');

		$this->loadModel('Category');
		$categories = $this->Category->find('list');

		$this->loadModel('Release');
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
		$this->set(array(
			'calendar' => $calendar,
		));
	}
}