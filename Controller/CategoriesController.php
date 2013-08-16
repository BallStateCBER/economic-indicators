<?php
App::uses('AppController', 'Controller');
/**
 * Categories Controller
 *
 * @property Category $Category
 */
class CategoriesController extends AppController {
	public $paginate = array(
		'limit' => 100,
		'order' => 'Category.name ASC'
	);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->deny('add', 'edit', 'delete', 'index');
	}

	/**
	 * Index method, used by administrators
	 * @return void
	 */
	public function index() {
		$this->Category->recursive = 0;
		$this->set('categories', $this->paginate());
	}

	/**
	 * Browse, used by the general public
	 * @return void
	 */
	public function browse() {
		$this->Category->recursive = 0;
		if (isset($this->request->pass[0]) && isset($this->request->pass[1])) {
			$location_type = $this->request->pass[0];
			$result = $this->Category->LocationType->find('first', array(
				'conditions' => array('LocationType.name' => $location_type),
				'contain' => false
			));
			$loc_type_id = empty($result) ? null : $result['LocationType']['id'];
			$loc_display_name = empty($result) ? null : $result['LocationType']['display_name'];

			$frequency = $this->request->pass[1];
			$result = $this->Category->Frequency->find('first', array(
				'conditions' => array('Frequency.name' => $frequency),
				'contain' => false
			));
			$frequency_id = empty($result) ? null : $result['Frequency']['id'];
		}

		if ($loc_type_id && $frequency_id) {
			$this->set(array(
				'title_for_layout' => "Categories: $loc_display_name $frequency",
				'categories' => $this->paginate('Category', array(
					'Category.location_type_id' => $loc_type_id,
					'Category.frequency_id' => $frequency_id
				))
			));
		} else {
			$this->set(array(
				'title_for_layout' => 'Categories',
				'categories' => $this->paginate()
			));
		}
	}

	/**
	 * view method
	 *
	 * @param string $id
	 * @return void
	 */
	public function view($category_id = null) {
		$this->Category->id = $category_id;
		if (!$this->Category->exists()) {
			throw new NotFoundException(__('Invalid category'));
		}

		// If this category only applies to one location,
		// redirect to its dataset instead of presenting location choices
		$loc_type_id = $this->Category->field('location_type_id');

		// If 'county', direct to 'all counties'
		if ($loc_type_id == 4) {
			$this->redirect(array(
				'controller' => 'datasets',
				'action' => 'view',
				'category_id' => $category_id,
				'location_id' => $this->Category->LocationType->Location->getAllCountiesId()
			));
		}
		$locations = $this->Category->LocationType->Location->find('list', array(
			'conditions' => array('Location.location_type_id' => $loc_type_id)
		));
		if (count($locations) === 1) {
			$location_id = reset(array_keys($locations));
			$this->redirect(array(
				'controller' => 'datasets',
				'action' => 'view',
				'category_id' => $category_id,
				'location_id' => $location_id
			));
		}

		$this->Category->LocationType->id = $loc_type_id;
		switch ($this->Category->LocationType->field('name')) {
			case 'msa':
				$loc_name_postfix = ' MSA';
				break;
			case 'county':
				$loc_name_postfix = ' County';
				break;
			default:
				$loc_name_postfix = null;
		}

		$this->set(array(
			'title_for_layout' => $this->Category->field('name'),
			'category_id' => $category_id,
			'locations' => $locations,
			'loc_name_postfix' => $loc_name_postfix
		));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Category->create();
			if ($this->Category->save($this->request->data)) {
				$this->Flash->success(__('The category has been saved'));
				$this->request->data['Category']['name'] = '';
				//$this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The category could not be saved. Please, try again.'));
			}
		}
		$this->set('frequencies', $this->Category->Frequency->find('list'));
		$this->set('locationTypes', $this->Category->LocationType->find('list'));
	}

	/**
	 * edit method
	 *
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		$this->Category->id = $id;
		if (!$this->Category->exists()) {
			throw new NotFoundException(__('Invalid category'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Category->save($this->request->data)) {
				$this->Flash->success(__('The category has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The category could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Category->read(null, $id);
		}
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
		$this->Category->id = $id;
		if (!$this->Category->exists()) {
			throw new NotFoundException(__('Invalid category'));
		}
		if ($this->Category->delete()) {
			$this->Flash->success(__('Category deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Flash->error(__('Category was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * If the categories table is empty, populates it
	 */
	public function import() {
		if ($this->Category->find('count')) {
			$this->Flash->notification('Import cannot take place while categories table is populated. Clear the table before repeating.');
			$this->redirect('/');
		}

		$category_groups_list = $this->Category->CategoryGroup->find('list');
		$frequencies = $this->Category->Frequency->find('list');
		foreach ($frequencies as $fid => $frequency) {
			// Just keep the lowercase first word of each frequency name
			$frequencies[$fid] = strtolower(reset(explode(' ', $frequency)));
		}
		$this->Category->LocationType->displayField = 'name';
		$location_types = $this->Category->LocationType->find('list');

		$categories = array(
			'country' => array(
				'Pay and Benefits' => array(
					'Average Weekly earnings (Monthly)',
					'Average Weekly hours (Monthly)',
					'Personal Income and Savings (monthly)'
				),
				'Indexes' => array(
					'Producer Price Index (Monthly)',
					'Consumer Price Index (Monthly)',
					'Inflation Expectation Index (Monthly)'
				),
				'Employment' => array(
					'Unemployment Rate (Monthly)',
					'Manufacturing Employment by state (Monthly)',
					'Unemployment Rates by State (Monthly)',
					'Number of establishments by 2-digit NACIS codes (annual)'
				),
				'Economic Accounts' => array(
					'Gross Domestic Product (Quarterly)',
					'Population (Annual)',
					'Annual Personal Income (Annual)',
					'Personal Income (quarterly)'
				),
				'Federal Reserve Data' => array(
					'Money Supply (weekly)',
					'Interest rates (weekly)',
					'Money Supply (Monthly)',
					'Industrial Production (Monthly)'
				),
				'Trade and Sales' => array(
					'Retail Sales (monthly)',
					'Housing Starts (monthly)',
					'Durable Goods Orders (monthly)',
					'Motor Vehicle Sales (monthly)'
				),
				'Prices' => array(
					'Gold (weekly)',
					'Oil (Weekly)'
				)
			),
			'state' => array(
				'Pay and Benefits' => array(
					'Average weekly earnings ($), Manufacturing (Monthly)',
					'Average weekly hours, Manufacturing (Monthly)',
					'Indiana Covered Wages (quarterly)'
				),
				'Employment' => array(
					'Establishment Employment (Monthly)',
					'Unemployment Rate (%) (Monthy)',
					'Number of establishments by 2-digit NACIS codes (annual)',
					'Employment (Annual)',
					'Unemployment Rate (%) (Annual)',
					'Indiana Covered Employment (Quarterly)'
				),
				'Economic Accounts' => array(
					'Population (annual)',
					'Personal Income (Annual)'
				),
				'Others' => array(
					'Building Permits (Monthly)'
				)
			),
			'msa' => array(
				'Employment' => array(
					'Unemployment Rate (%) (Monthly)',
					'Unemployment Rate (annual)',
					'Establishment Employment (Monthly)',
					'Employment (annual)'
				),
				'Economic Accounts' => array(
					'Annual Personal Income (annual)'
				),
				'Others' => array(
					'Building Permits (Monthly)',
					'Population (Annual)'
				)
			),
			'county' => array(
				'Employment' => array(
					'Number of establishments by 2-digit NACIS codes (annual)',
					'Household Employment (Monthly)',
					'Unemployment Rate (%) (monthly)',
					'Employment (annual)',
					'Unemployment Rate (%) (annual)'
				),
				'Economic Accounts' => array(
					'Annual Personal Income (annual)'
				),
				'Others' => array(
					'Building Permits (Monthly)',
					'Population (annual)'
				)
			)
		);

		foreach ($categories as $location_type => $category_groups) {
			$loc_type_id = array_search($location_type, $location_types);
			if ($loc_type_id === false) {
				$this->Flash->error("Could not find a location type matching '$location_type'.");
				continue;
			}
			foreach ($category_groups as $category_group => $categories) {
				$cat_group_id = array_search($category_group, $category_groups_list);
				if ($cat_group_id === false) {
					$this->Flash->error("Could not find a category group matching '$category_group'.");
					continue;
				}
				foreach ($categories as $category) {
					$frequency_start = strrpos($category, '(') + 1;
					$frequency_length = strrpos($category, ')') - $frequency_start;
					$frequency = strtolower(substr($category, $frequency_start, $frequency_length));
					$frequency_id = array_search($frequency, $frequencies);
					if ($frequency_id === false) {
						$this->Flash->error("Could not find a frequency matching '$frequency'.");
						continue;
					}
					$category = ucwords(trim(substr($category, 0, $frequency_start - 1)));
					$this->Category->create(array(
						'name' => $category,
						'frequency_id' => $frequency_id,
						'location_type_id' => $loc_type_id,
						'category_group_id' => $cat_group_id
					));
					$row_details_for_message = "<strong>$category</strong> ($loc_type_id/$cat_group_id/$frequency_id)";
					if ($this->Category->save()) {
						$this->Flash->success("Added $row_details_for_message");
					} else {
						$this->Flash->error("Error adding $row_details_for_message");
					}
				}
			}
		}
		$this->redirect('/');
	}
}
