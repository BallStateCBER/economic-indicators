<?php
App::uses('AppModel', 'Model');
class Category extends AppModel {
	public $name = 'Category';
	public $hasMany = array('Dataset');
	public $belongsTo = array('Frequency', 'LocationType', 'CategoryGroup');
	public $actsAs = array('Sluggable.Sluggable' => array(
		'label' => 'name',
		'slug' => 'slug',
		'separator' => '-',
		'overwrite' => true   
	));
	
	/*
	public function getSlugList($scope) {
		$cache_key = "{$scope}CategorySlugList";
		if ($cached = Cache::read($cache_key)) {
			return $cached;
		}
		$categories = $this->find('list', array(
			'conditions' => array('scope' => $scope),
			'order' => 'name ASC',
			'contain' => false
		));
		if (empty($categories)) {
			throw new InternalErrorException("No categories found with scope \"$scope\"");
		}
		Cache::write($cache_key, $categories);
        return $categories;
	}
	*/
	
	/**
	 * Returns an array with the structure $categories[$location_type][$frequency][$category_id] = $category;
	 * @return array
	 */
	public function getArranged() {
		$frequencies = $this->Frequency->getList();
		$location_types = $this->LocationType->getList();
		$results = $this->find('all', array(
			'contain' => false,
			'order' => 'Category.name ASC'
		));
		$categories = array();
		foreach ($results as $result) {
			$loc_type_id = $result['Category']['location_type_id'];
			$location_type = $location_types[$loc_type_id];
			$frequency_id = $result['Category']['frequency_id'];
			$frequency = $frequencies[$frequency_id];
			$category_id = $result['Category']['id'];
			$categories[$location_type][$frequency][$category_id] = $result['Category']; 
		}
		return $categories;
	}
	
	/**
	 * Returns an array to be used as the options for a Category <select> menu, with the keys that correspond to <option value=""> beginning with "null". 
	 * @return array
	 */
	public function getSelectOptions() {
		$arranged_categories = $this->getArranged();
		$options = array();
		$options["null1"] = '';
		$n = 1;
		foreach ($arranged_categories as $location_type => $frequencies) {
			foreach ($frequencies as $frequency => $categories) {
				
				// Display 'country' as 'USA' so it's not confused with 'county'
				if ($location_type == 'country') {
					$location_type = 'USA';	
				}
				
				$options["null$n"] = strtoupper("$location_type - $frequency");
				$n++;
				foreach ($categories as $category_id => $category_name) {
					$options[$category_id] = " - $category_name";
				}
				$options["null$n"] = '';
				$n++;
			}
		}
		
		// Remove the last (blank) option
		array_pop($options);
		
		return $options;
	}
	
	/**
	 * Takes an array of categories and arranges them into groups according to their names
	 * @param array $categories
	 * @return array
	 */
	public function arrangeByGroup($categories) {
		// Establish the order of groups
		$category_groups = $this->CategoryGroup->find('list', array(
			'order' => array(
				'CategoryGroup.name = \'Others\' ASC',
				'CategoryGroup.name ASC'
			)
		));
		$retval = array();
		foreach ($category_groups as $group_id => $group_name) {
			$retval[$group_name] = array();
		}
		
		// Place categories in groups
		foreach ($categories as $category) {
			if (isset($category['Category']['category_group_id'])) {
				$group_id = $category['Category']['category_group_id'];
				$category_name = $category['Category']['name'];
			} elseif (isset($category['category_group_id'])) {
				$group_id = $category['category_group_id'];
				$category_name = $category['name'];
			} else {
				throw new InternalErrorException('Invalid category');
			}
			$group_name = $category_groups[$group_id];
			$retval[$group_name][$category_name] = $category;
		}
		
		// Remove any empty groups and sort
		foreach ($retval as $group => $categories) {
			if (empty($categories)) {
				unset($retval[$group]);
			} else {
				ksort($retval[$group]);
			}
		}
		return $retval;
	}
	
	/**
	 * Returns an array with the IDs of Categories with Datasets
	 * @return array
	 */
	public function getIdsInUse() {
		$result = $this->Dataset->find('all', array(
			'fields' => array('DISTINCT Dataset.category_id'),
			'contain' => false
		));
		return Hash::extract($result, '{n}.Dataset.category_id');
	}
}