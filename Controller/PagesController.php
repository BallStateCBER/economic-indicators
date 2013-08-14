<?php
App::uses('AppController', 'Controller');
class PagesController extends AppController {
	public $name = 'Pages';
	public $uses = array();
	
	private function __prepareCalendar() {
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
	
	public function home() {
		// Prepare data for release calendar
		$this->__prepareCalendar();
		
		$this->set(array(
			'title_for_layout' => ''
		));
	}
	
	public function clear_cache() {
		Cache::clear();
		clearCache();
		$this->Flash->success('Cache cleared');
		$this->set(array(
			'title_for_layout' => 'Clear Cache'
		));
		return $this->render('/Pages/home');
	}
	
	public function data() {
		// Assumption: Each location has a unique slug
		$location_slug = $this->params['pass'][0];
		
		$category_slug = $this->params['pass'][1];
		
		$this->set(array(
			'title_for_layout' => 'Clear Cache'
		));
	}
}