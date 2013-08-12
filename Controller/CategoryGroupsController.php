<?php
App::uses('AppController', 'Controller');
/**
 * CategoryGroups Controller
 *
 * @property CategoryGroup $CategoryGroup
 */
class CategoryGroupsController extends AppController {
	
	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function view() {
		if (isset($this->request->params['category_group_id'])) {
			$category_group_id = $this->request->params['category_group_id'];
		} else {
			throw new NotFoundException('No category group specified');
		}
		if (isset($this->request->params['location_type_id'])) {
			$location_type_id = $this->request->params['location_type_id'];
		} else {
			throw new NotFoundException('No location type specified');
		}
		
		// Get a list of categories with datasets
		$this->loadModel('Category');
		$used_category_ids = $this->Category->getIdsInUse();
		
		// Get CategoryGroup data and relevant Categories
		$category_group = $this->CategoryGroup->find('first', array(
			'conditions' => array(
				'CategoryGroup.id' => $category_group_id
			),
			'contain' => array(
				'Category' => array(
					'conditions' => array(
						'Category.id' => $used_category_ids,
						'Category.location_type_id' => $location_type_id
					)
				)
			)
		));
		if (empty($category_group)) {
			throw new NotFoundException("Category group #$category_group_id not found");
		}
		
		// Get basic info about this LocationType
		$this->loadModel('LocationType');
		$this->LocationType->id = $location_type_id;
		$location_type = $this->LocationType->find('first', array(
			'conditions' => array('LocationType.id' => $location_type_id),
			'contain' => false
		));
		if (empty($location_type)) {
			throw new NotFoundException("Location type #$location_type not found");
		}
		
		$this->loadModel('Frequency');
		$loc_type_name = $location_type['LocationType']['display_name'];
		$cat_group_name = 
		$this->set(array(
			'title_for_layout' => "",
			'frequencies' => $this->Frequency->find('list')
		));
		$this->set(compact('location_type', 'category_group'));
	}
}