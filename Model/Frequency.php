<?php
App::uses('AppModel', 'Model');
class Frequency extends AppModel {
	public $name = 'Frequency';
	public $hasMany = array('Category');
	
	public function getList() {
		$cache_key = "frequencyList";
		if (Configure::read('use_cache') && $cached = Cache::read($cache_key)) {
			return $cached;
		}
		$result = $this->find('list');
		if (Configure::read('use_cache')) {
			Cache::write($cache_key, $result);
		}
        return $result;
	}

}