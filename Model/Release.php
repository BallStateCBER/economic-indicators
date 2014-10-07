<?php
App::uses('AppModel', 'Model');
/**
 * Release Model
 *
 * @property Category $Category
 */
class Release extends AppModel {
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'category_id';
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'category_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'date' => array(
			'date' => array(
				'rule' => array('date'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Category' => array(
			'className' => 'Category',
			'foreignKey' => 'category_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function isScheduled($category_id, $date) {
		$result = $this->find('first', array(
			'conditions' => array(
				'Release.category_id' => $category_id,
				'Release.date' => $date['year'].'-'.$date['month'].'-'.$date['day']
			),
			'fields' => array('Release.id'),
			'contain' => false
		));
		return empty($result) ? null : $result['Release']['id'];
	}

	public function getUpcoming() {
		return $this->find('all', array(
			'conditions' => array(
				'Release.date >=' => date('Y-m-d')
			),
			'order' => 'Release.date ASC'
		));
	}

	/**
	 * Retrieves releases dated from 3 months ago into the future.
	 * @return array
	 */
	public function getUpcomingAndRecent() {
		return $this->find('all', array(
			'conditions' => array(
				'Release.date >=' => date('Y-m-d', strtotime('now - 3 months'))
			),
			'order' => 'Release.date ASC'
		));
	}

	public function getAll() {
		return $this->find('all', array(
			'order' => 'Release.date ASC'
		));
	}

	/**
	 * Returns the date (YYYY-MM-DD) of the next release for the specified category
	 * @param int $category_id
	 * @return string|NULL
	 */
	public function getNextUpcoming($category_id) {
		$result = $this->find('first', array(
			'conditions' => array(
				'Release.category_id' => $category_id,
				'Release.date >=' => date('Y-m-d')
			),
			'order' => 'Release.date ASC'
		));
		return empty($result) ? null : $result['Release']['date'];
	}

	/**
	 * Returns the date (YYYY-MM-DD) of the most recent release for the specified category
	 * @param int $category_id
	 * @return string|NULL
	 */
	public function getMostRecent($category_id) {
		$result = $this->find('first', array(
			'conditions' => array(
				'Release.category_id' => $category_id,
				'Release.date <' => date('Y-m-d')
			),
			'order' => 'Release.date DESC'
		));
		return empty($result) ? null : $result['Release']['date'];
	}
}
