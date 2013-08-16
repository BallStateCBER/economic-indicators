<?php
App::uses('AppController', 'Controller');
class PagesController extends AppController {
	public $name = 'Pages';
	public $uses = array();

	public function home() {
		// Prepare data for release calendar
		$this->__setUpcomingReleases();

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