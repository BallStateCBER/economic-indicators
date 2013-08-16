<?php
App::uses('AppModel', 'Model');
class LocationType extends AppModel {
	public $displayField = 'display_name';
	public $name = 'LocationType';
	public $hasMany = array('Location', 'Category');
	public $order = 'LocationType.weight ASC';

	public function getList() {
		$cache_key = "locationTypeList";
		if (Configure::read('use_cache') && $cached = Cache::read($cache_key)) {
			return $cached;
		}
		$result = $this->find('list');
		if (Configure::read('use_cache')) {
			Cache::write($cache_key, $result);
		}
        return $result;
	}

	public function getLocationsOfTypeAndCategories($id) {
		$cache_key = "getLocationsOfTypeAndCategories($id)";
		if (Configure::read('use_cache') && $cached = Cache::read($cache_key)) {
			return $cached;
		}
		$result = $this->find('all', array(
			'order' => 'name ASC',
			'conditions' => array('id' => $id),
			'contain' => array(
				'Location' => array('order' => 'Location.name ASC'),
				'Category' => array('order' => 'Category.name ASC')
			)
		));
		if (empty($result)) {
			throw new InternalErrorException("No locations found with type \"$id\"");
		}
		if (Configure::read('use_cache')) {
			Cache::write($cache_key, $result);
		}
        return $result;
	}

	public function getCountries() {
		return $this->getLocationsOfTypeAndCategories(1);
	}

	public function getStates() {
		return $this->getLocationsOfTypeAndCategories(2);
	}

	public function getMsas() {
		return $this->getLocationsOfTypeAndCategories(3);
	}

	public function getCounties() {
		return $this->getLocationsOfTypeAndCategories(4);
	}

	/**
	 * Returns the ID of the default location for a location type
	 * @param string|int $loc_type_name
	 * @return int
	 */
	public function getDefaultLocationId($loc_type) {
		if (is_numeric($loc_type)) {
			$loc_type_id = $loc_type;
		} else {
			$result = $this->find('first', array(
				'conditions' => array(
					'LocationType.name' => $loc_type
				),
				'fields' => array('LocationType.id'),
				'contain' => false
			));
			if (empty($result)) {
				throw new InternalErrorException("Location type \"$loc_type\" not recognized");
			}
			$loc_type_id = $result['LocationType']['id'];
		}

		$result = $this->Location->find('first', array(
			'conditions' => array(
				'Location.location_type_id' => $loc_type_id
			),
			'fields' => array('Location.id'),
			'order' => 'Location.weight ASC',
			'contain' => false
		));
		return empty($result) ? null : $result['Location']['id'];
	}
}