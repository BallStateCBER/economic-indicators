<?php
App::uses('AppModel', 'Model');
class Location extends AppModel {
	public $name = 'Location';
	public $hasMany = array('Dataset');
	public $belongsTo = array('LocationType');
	public $displayField = 'name';
	
	public function getLocationsOfType($type_id) {
		$cache_key = "LocType{$type_id}List";
		if (Configure::read('use_cache') && $cached = Cache::read($cache_key)) {
			return $cached;
		}
		$locations = $this->find('list', array(
			'order' => 'name ASC',
			'conditions' => array('location_type_id' => $type_id),
			'contain' => false
		));
		if (empty($locations)) {
			throw new InternalErrorException("No locations found with type \"$type_id\"");
		}
		$this->LocationType->id = $type_id;
		$display_name = $this->LocationType->field('display_name');
		$retval = array($display_name => $locations);
		if (Configure::read('use_cache')) {
			Cache::write($cache_key, $retval);
		}
        return $retval;
	}
	
	public function getCountries() {
		return $this->getLocationsOfType(1);
	}
	
	public function getStates() {
		return $this->getLocationsOfType(2);
	}

	public function getMsas() {
		return $this->getLocationsOfType(3);
	}
	
	public function getCounties() {
		return $this->getLocationsOfType(4);
	}
	
	/**
	 * Returns an array of location slugs => names for the options of a select field
	 * @return array
	 */
	public function getSelectOptions() {
		$cache_key = "LocationGetSelectOptions";
		if (Configure::read('use_cache') && $cached = Cache::read($cache_key)) {
			return $cached;
		}
		$location_types = $this->LocationType->find('list');
		$locations = $this->find('all', array(
			'order' => 'name ASC',
			'contain' => false
		));
		$categorized_locations = array();
		foreach ($locations as $location) {
			$type_id = $location['Location']['location_type_id'];
			$type = $location_types[$type_id];
			$name = '- '.$location['Location']['name'];
			$slug = $location['Location']['slug'];
			$categorized_locations[$type][$slug] = $name;
		}
		$types = array_keys($categorized_locations);
		$retval = array();
		$retval[] = '';
		foreach ($types as $type) {
			$retval[] = strtoupper($type);
			$retval = array_merge($retval, $categorized_locations[$type]);
			$retval[] = '';
		}
		
		// Remove trailing blank option
		array_pop($retval);
		
		if (Configure::read('use_cache')) {
			Cache::write($cache_key, $retval);
		}
		return $retval;
	}
	
	/**
	 * Accepts slug of a location's name and returns its ID
	 * @param string $slug
	 * @return int $id
	 */
	public function getIdFromSlug($slug) {
		$cache_key = "getLocationIdFromSlug($slug)";
		if (Configure::read('use_cache') && $cached = Cache::read($cache_key)) {
			return $cached;
		}
		$result = $this->find('first', array(
			'fields' => array('id'),
			'conditions' => array('slug' => $slug),
			'contain' => false
		));
		$retval = (int) $result['Location']['id'];
		if (Configure::read('use_cache')) { 
			Cache::write($cache_key, $retval);
		}
		return $retval;
	}
	
	public function getArranged() {
		$cache_key = "getArrangedLocations";
		if (Configure::read('use_cache') && $cached = Cache::read($cache_key)) {
			return $cached;
		}
		$location_types = $this->LocationType->getList();
		$results = $this->find('all', array(
			'contain' => false,
			'order' => array('weight ASC', 'name ASC')
		));
		$locations = array();
		foreach ($results as $result) {
			$loc_type_id = $result['Location']['location_type_id'];
			$location_type = $location_types[$loc_type_id];
			$location_id = $result['Location']['id'];
			$location_name = $result['Location']['name'];
			$locations[$location_type][$location_id] = $location_name; 
		}
		$retval = $locations;
		if (Configure::read('use_cache')) { 
			Cache::write($cache_key, $retval);
		}
		return $retval;
	}
	
	/**
	 * Returns the name of a location with "County" or "MSA" appended when appropriate
	 * @param int $location_id
	 * @return string
	 */
	public function getFullName($location_id) {
		$cache_key = "getFullName($location_id)";
		if (Configure::read('use_cache') && $cached = Cache::read($cache_key)) {
			return $cached;
		}
		$this->id = $location_id;
		$name = $this->field('name');
		$this->LocationType->id = $this->field('location_type_id');
		switch ($this->LocationType->field('name')) {
			case 'msa':
				if ($location_id != $this->getAllMSAsId()) {
					$name = "$name MSA";
				}
				break;
			case 'county':
				if ($location_id != $this->getAllCountiesId()) {
					$name = "$name County";
				}
				break;
		}
		$retval = $name;
		if (Configure::read('use_cache')) { 
			Cache::write($cache_key, $retval);
		}
		return $retval;
	}
	
	public function getAllCountiesId() {
		$cache_key = "getAllCountiesId()";
		if (Configure::read('use_cache') && $cached = Cache::read($cache_key)) {
			return $cached;
		}
		$result = $this->find('first', array(
			'conditions' => array('name' => 'All Indiana Counties'),
			'fields' => array('id'),
			'contain' => false
		));
		$retval = (int) $result['Location']['id'];
		if (Configure::read('use_cache')) { 
			Cache::write($cache_key, $retval);
		}
		return $retval;
	}
	
	public function getAllMSAsId() {
		$cache_key = "getAllMSAsId()";
		if (Configure::read('use_cache') && $cached = Cache::read($cache_key)) {
			return $cached;
		}
		$result = $this->find('first', array(
			'conditions' => array('name' => 'All Indiana MSAs'),
			'fields' => array('id'),
			'contain' => false
		));
		$retval = (int) $result['Location']['id'];
		if (Configure::read('use_cache')) { 
			Cache::write($cache_key, $retval);
		}
		return $retval;
	}
}