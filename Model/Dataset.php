<?php
App::uses('AppModel', 'Model');
class Dataset extends AppModel {
	public $name = 'Dataset';
	public $belongsTo = array('Location', 'Category');
	public $validate = array(
		'location_id' => array(
			'rule'    => 'notBlank',
			'message' => 'Location required',
			'allowEmpty' => false
		),
		'category_id' => array(
			'rule'    => 'notBlank',
			'message' => 'Category required',
			'allowEmpty' => false
		),
		'code' => array(
		    'notblank' => array(
                'rule'    => 'notBlank',
                'message' => 'This field cannot be left blank',
                'allowEmpty' => false
            ),
            'notHttp' => array(
                'rule' => array('notHttp'),
                'message' =>
                    'Iframe content must be served over HTTPS, rather than HTTP. ' .
                    'Please replace src="http:// with src="https:// in this dataset\'s code.'
            )
		),
	);

    /**
     * Returns TRUE if nothing in the code uses a source served over HTTP
     *
     * @param array $check Array of ['code' => 'code for this dataset']
     * @return bool
     */
	public function notHttp($check) {
	    $code = $check['code'];

	    return stripos($code, 'src="http://') === false;
    }

	public function getByCategoryandLocation($category_id, $location_id = null) {
		// Get Dataset via location and category
		if ($location_id) {
			return $this->find('first', array(
				'conditions' => compact('category_id', 'location_id')
			));
		}

		// If no location is specified,
		// find the first location with a dataset in this category
		$this->Category->id = $category_id;
		$location_type_id = $this->Category->field('location_type_id');
		$location_list = $this->Location->find('list', array(
			'conditions' => array('location_type_id' => $location_type_id)
		));
		return $this->find('first', array(
			'conditions' => array(
				'Dataset.category_id' => $category_id,
				'Dataset.location_id' => array_keys($location_list)
			)
		));
	}
}