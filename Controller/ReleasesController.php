<?php
App::uses('AppController', 'Controller');
/**
 * Releases Controller
 *
 * @property Release $Release
 */
class ReleasesController extends AppController {
	public $components = array('Session');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->deny('add', 'edit', 'delete');
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$releases = $this->Release->getUpcoming();
		$structure = array();
		foreach ($releases as $release) {
			$loc_type_id = $release['Category']['location_type_id'];
			$cat_group_id = $release['Category']['category_group_id'];
			$cat_id = $release['Category']['id'];
			$date = $release['Release']['date'];
			$release_id = $release['Release']['id'];
			$structure[$loc_type_id][$cat_group_id][$cat_id][$date] = $release_id;
		}
		$this->loadModel('Category');
		$this->loadModel('CategoryGroup');
		$this->loadModel('LocationType');
		$this->LocationType->displayField = 'display_name'; // not sure why this isn't already the default
		$this->set(array(
			'title_for_layout' => 'Upcoming Data Releases',
			'categories' => $this->Category->find('list'),
			'category_groups' => $this->CategoryGroup->find('list'),
			'location_types' => $this->LocationType->find('list'),
			'releases' => $releases,
			'structure' => $structure
		));
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Release->id = $id;
		if (!$this->Release->exists()) {
			throw new NotFoundException(__('Invalid release'));
		}
		$this->set('release', $this->Release->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Release->create();

			// Deduce location_id from location_type_id
			//$this->loadModel('LocationType');
			//$loc_type_name = $this->request->data['Release']['location_type_name'];
			//$location_id = $this->LocationType->getDefaultLocationId($loc_type_name);
			$category_id = $this->request->data['Release']['category_id'];
			$date = $this->request->data['Release']['date'];
			$is_redundant = $this->Release->isScheduled($category_id, $date);

			// If this has already been entered, don't add another,
			// but still display a success message.
			if ($is_redundant || $this->Release->save($this->request->data)) {
				$this->Flash->success('The release has been saved');
			} else {
				$this->Flash->error('The release could not be saved. Please try again.');
			}
		}
		$this->loadModel('Category');
		$this->loadModel('Frequency');
		$this->loadModel('LocationType');
		$this->set(array(
			'title_for_layout' => 'Add an Anticipated Release Date',
			'categories' => $this->Category->getArranged(),
			'frequencies' => $this->Frequency->find('list'),
			'location_types' => $this->LocationType->find('list')
		));
		$this->render('/Releases/form');
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Release->id = $id;
		if (! $this->Release->exists()) {
			throw new NotFoundException(__('Invalid release'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			// Deduce location_id from location_type_id
			//$this->loadModel('LocationType');
			//$loc_type_name = $this->request->data['Release']['location_type_name'];
			//$location_id = $this->LocationType->getDefaultLocationId($loc_type_name);
			$category_id = $this->request->data['Release']['category_id'];
			$date = $this->request->data['Release']['date'];
			$existing_release_id = $this->Release->isScheduled($category_id, $date);

			if ($existing_release_id && $existing_release_id != $id) {
				$this->Flash->error('There\'s already another release (#'.$existing_release_id.') for that category (#'.$category_id.') on that date ('.implode('-', $date).').');
			} elseif ($this->Release->save($this->request->data)) {
				$this->Flash->success(__('The release has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error('The release could not be saved. Please try again.');
			}
		} else {
			$this->request->data = $this->Release->read(null, $id);
		}
		$this->loadModel('Category');
		$this->loadModel('Frequency');
		$this->loadModel('LocationType');
		$this->set(array(
			'title_for_layout' => 'Edit Anticipated Release Date',
			'categories' => $this->Category->getArranged(),
			'frequencies' => $this->Frequency->find('list'),
			'location_types' => $this->LocationType->find('list')
		));
		$this->render('/Releases/form');
	}

/**
 * delete method
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Release->id = $id;
		if (!$this->Release->exists()) {
			throw new NotFoundException(__('Invalid release'));
		}
		if ($this->Release->delete()) {
			$this->Flash->success(__('Release deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Flash->error(__('Release was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

	// Page provided for Victoria to copy and paste content into emails
	public function upcoming() {
		$this->__setUpcomingReleases();
		$this->set('title_for_layout', 'Upcoming Releases');
	}
}
