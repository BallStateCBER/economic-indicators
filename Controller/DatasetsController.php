<?php
App::uses('AppController', 'Controller');
class DatasetsController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->deny('add', 'edit', 'delete', 'index');
	}
	
	public function index() {
		$this->Dataset->recursive = 0;
		$this->set('datasets', $this->paginate());
	}

	/**
	 * View a data set
	 * @param int $category_id
	 * @param int $location_id
	 * @throws NotFoundException
	 */
	public function view($category_id = null, $location_id = null) {
		if (! $category_id) {
			throw new BadRequestException('Could not load data set. Category not specified.');
		}

		$dataset = $this->Dataset->getByCategoryandLocation($category_id, $location_id);
		
		$this->loadModel('Category');
		$this->Category->id = $category_id;
		if (! $this->Category->exists()) {
			throw new NotFoundException('Could not load data set. Category not found.');
		}
		$category_name = $this->Category->field('name');
		
		$this->loadModel('CategoryGroup');
		$category_group_id = $this->Category->field('category_group_id');
		$this->CategoryGroup->id = $category_group_id;
		$category_group = $this->CategoryGroup->field('name');
		
		$this->loadModel('Frequency');
		$this->Frequency->id = $this->Category->field('frequency_id');
		$frequency = $this->Frequency->field('name');
		// Just the first word
		$frequency = strtolower(reset(explode(' ', $frequency)));
		
		$this->loadModel('Release');
		$next_release = $this->Release->getNextUpcoming($category_id);
		$prev_release = $this->Release->getMostRecent($category_id);
		
		$this->loadModel('Location');
		$location_type_id = $this->Category->field('location_type_id');
		if (! $location_id) {
			// Get location from dataset
			if (isset($dataset['Dataset']['location_id'])) {
				$location_id = $dataset['Dataset']['location_id'];
			// Get default location for this category
			} else {
				$this->loadModel('LocationType');
				$location_id = $this->LocationType->getDefaultLocationId($location_type_id);
			}
		}
		$this->Location->id = $location_id;
		$location_name = $this->Location->getFullName($location_id);
			
		if (empty($dataset)) {
			$title_for_layout = 'Data set not found';
			$this->response->statusCode(404);
		} else {		
			$title_for_layout = "$category_name ($location_name / $frequency)";
		}
		
		$this->set(compact(
			'title_for_layout',
			'next_release',
			'prev_release',
			'category_group',
			'category_group_id',
			'category_name',
			'location_name',
			'location_type_id',
			'frequency',
			'dataset'
		));
	}

	public function add() {
		if ($this->request->is('post')) {
			/*
			$this->Dataset->Location->id = $this->request->data['Dataset']['location_id'];
			$this->request->data['Dataset']['location_name'] = $this->Dataset->Location->field('name');
			$this->Dataset->Category->id = $this->request->data['Dataset']['category_id'];
			$this->request->data['Dataset']['category_name'] = $this->Dataset->Category->field('name');
			*/
			
			$this->Dataset->create();
			
			// Check to see if this should overwrite an existing dataset
			$overwriting = false;
			$location_id = $this->request->data['Dataset']['location_id'];
			$category_id = $this->request->data['Dataset']['category_id'];
			$result = $this->Dataset->find('list', array(
				'conditions' => compact('location_id', 'category_id')
			));
			if (! empty($result)) {
				$this->Dataset->id = reset(array_keys($result));
				$overwriting = true;
			}
			
			if ($this->Dataset->save($this->request->data)) {
				$verb = $overwriting ? 'overwritten' : 'saved';
				$this->Flash->success("The dataset has been $verb");
				$this->request->data = array();
			} else {
				$this->Flash->error("The dataset could not be $verb. Please, try again.");
			}
		}
		$locations = $this->Dataset->Location->getArranged();
		$categories = $this->Dataset->Category->getArranged();
		$this->set(compact('locations', 'categories'));
	}

	public function edit($id = null) {
		$this->Dataset->id = $id;
		if (!$this->Dataset->exists()) {
			throw new NotFoundException(__('Invalid dataset'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Dataset->save($this->request->data)) {
				$this->Flash->set('The dataset has been saved');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->set('The dataset could not be saved. Please, try again.');
			}
		} else {
			$this->request->data = $this->Dataset->read(null, $id);
		}
		$locations = $this->Dataset->Location->find('list');
		$categories = $this->Dataset->Category->find('list');
		$this->set(compact('locations', 'categories'));
	}

	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Dataset->id = $id;
		if (!$this->Dataset->exists()) {
			throw new NotFoundException(__('Invalid dataset'));
		}
		if ($this->Dataset->delete()) {
			$this->Flash->set('Dataset deleted');
			$this->redirect(array('action' => 'index'));
		}
		$this->Flash->set('Dataset was not deleted');
		$this->redirect(array('action' => 'index'));
	}
}