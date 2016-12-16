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
		$releases = $this->Release->getUpcomingAndRecent();
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
		$this->set(array(
			'title_for_layout' => 'Recent and Upcoming Data Releases',
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
		$this->loadModel('LocationType');
		$location_type_list = $this->LocationType->find('list');

		$this->loadModel('Category');
		$categories = $this->Category->find('list');

		$this->loadModel('Frequency');
		$frequencies = $this->Frequency->find('list');

		$releases = $this->Release->getUpcoming();
		$arranged_releases = array();
		foreach ($releases as $release) {
			// Skip if this is a release for a Category that has been deleted
			if (! isset($release['Category']['name']) || empty($release['Category']['name'])) {
				continue;
			}
			$date = $release['Release']['date'];
			$loc_type_id = $release['Category']['location_type_id'];
			$location_type_name = $location_type_list[$loc_type_id];
			$category_id = $release['Release']['category_id'];
			$category = $categories[$category_id];
			$frequency_id = $release['Category']['frequency_id'];
			$frequency_words = explode(' ', $frequencies[$frequency_id]);
			$frequency = $frequency_words[0];
			$arranged_releases[$date][] = compact('location_type_name', 'category', 'frequency');
		}

		$this->set(array(
			'releases' => $arranged_releases,
			'title_for_layout' => 'Upcoming Releases'
		));
	}

	public function import() {
	    $data = '
	        Producer Price Index	United States	1/13/2017
            Consumer Price Index	United States	1/18/2017
            Retail Sales	United States	1/13/2017
            Gross Domestic Product	United States	1/27/2017
            Durable Good Orders	United States	1/27/2017
            Motor Vehicle Retail Sales	United States	1/6/2017
            Industrial Production	United States	1/18/2017
            Housing Starts	United States	1/19/2017
            Unemployment Rates By State	United States	1/24/2017
            Manufacturing Employment By State	United States	1/24/2017
            Unemployment Rate	United States	1/6/2017
            Employment, Hours, and Earnings by Sector	United States	1/6/2017
            (state) Employment, Hours, and Earnings by Sector	Indiana	1/24/2017
            (state) Unemployment Rate	Indiana	1/24/2017
            (county) Unemployment Rate	Indiana MSAs	1/27/2017
            (msa) Unemployment Rate	Indiana MSAs	1/27/2017
            (msa) Employment by Sector	Indiana Counties	1/27/2017
            Producer Price Index	United States	2/14/2017
            Consumer Price Index	United States	2/15/2017
            Retail Sales	United States	2/15/2017
            Gross Domestic Product	United States	2/28/2017
            Durable Good Orders	United States	2/27/2017
            Motor Vehicle Retail Sales	United States	2/3/2017
            Industrial Production	United States	2/15/2017
            Housing Starts	United States	2/16/2017
            Unemployment Rates By State	United States	3/13/2017
            Manufacturing Employment By State	United States	3/13/2017
            Unemployment Rate	United States	2/3/2017
            Employment, Hours, and Earnings by Sector	United States	2/3/2017
            (state) Employment, Hours, and Earnings by Sector	Indiana	3/13/2017
            (state) Unemployment Rate	Indiana	3/13/2017
            (county) Unemployment Rate	Indiana MSAs	3/16/2017
            (msa) Unemployment Rate	Indiana MSAs	3/16/2017
            (msa) Employment by Sector	Indiana Counties	3/16/2017
            Producer Price Index	United States	3/14/2017
            Consumer Price Index	United States	3/15/2017
            Retail Sales	United States	3/15/2017
            Gross Domestic Product	United States	3/30/2017
            Durable Good Orders	United States	3/24/2017
            Personal Income	United States	3/28/2017
            Motor Vehicle Retail Sales	United States	3/3/2017
            Industrial Production	United States	3/17/2017
            Housing Starts	United States	3/16/2017
            Unemployment Rates By State	United States	3/24/2017
            Manufacturing Employment By State	United States	3/24/2017
            Unemployment Rate	United States	3/10/2017
            Employment, Hours, and Earnings by Sector	United States	3/10/2017
            (state) Employment, Hours, and Earnings by Sector	Indiana	3/24/2017
            (state) Unemployment Rate	Indiana	3/24/2017
            (county) Unemployment Rate	Indiana MSAs	3/27/2017
            (msa) Unemployment Rate	Indiana MSAs	3/27/2017
            (msa) Employment by Sector	Indiana Counties	3/27/2017
            Producer Price Index	United States	4/13/2017
            Consumer Price Index	United States	4/14/2017
            Retail Sales	United States	4/14/2017
            Gross Domestic Product	United States	4/28/2017
            Durable Good Orders	United States	4/27/2017
            Motor Vehicle Retail Sales	United States	4/7/2017
            Industrial Production	United States	4/18/2017
            Housing Starts	United States	4/18/2017
            Unemployment Rates By State	United States	4/21/2017
            Manufacturing Employment By State	United States	4/21/2017
            Unemployment Rate	United States	4/7/2017
            Employment, Hours, and Earnings by Sector	United States	4/7/2017
            (state) Employment, Hours, and Earnings by Sector	Indiana	4/21/2017
            (state) Unemployment Rate	Indiana	4/21/2017
            (county) Unemployment Rate	Indiana MSAs	4/24/2017
            (msa) Unemployment Rate	Indiana MSAs	4/24/2017
            (msa) Employment by Sector	Indiana Counties	4/24/2017
            Producer Price Index	United States	5/11/2017
            Consumer Price Index	United States	5/12/2017
            Retail Sales	United States	5/12/2017
            Gross Domestic Product	United States	5/26/2017
            Durable Good Orders	United States	5/26/2017
            Motor Vehicle Retail Sales	United States	5/5/2017
            Industrial Production	United States	5/16/2017
            Housing Starts	United States	5/16/2017
            Unemployment Rates By State	United States	5/19/2017
            Manufacturing Employment By State	United States	5/19/2017
            Unemployment Rate	United States	5/5/2017
            Employment, Hours, and Earnings by Sector	United States	5/5/2017
            (state) Employment, Hours, and Earnings by Sector	Indiana	5/19/2017
            (state) Unemployment Rate	Indiana	5/19/2017
            (county) Unemployment Rate	Indiana MSAs	5/22/2017
            (msa) Unemployment Rate	Indiana MSAs	5/22/2017
            (msa) Employment by Sector	Indiana Counties	5/22/2017
            Producer Price Index	United States	6/13/2017
            Consumer Price Index	United States	6/14/2017
            Retail Sales	United States	6/14/2017
            Gross Domestic Product	United States	6/29/2017
            Durable Good Orders	United States	6/26/2017
            Personal Income	United States	6/27/2017
            Motor Vehicle Retail Sales	United States	6/2/2017
            Industrial Production	United States	6/15/2017
            Housing Starts	United States	6/16/2017
            Unemployment Rates By State	United States	6/16/2017
            Manufacturing Employment By State	United States	6/16/2017
            Unemployment Rate	United States	6/2/2017
            Employment, Hours, and Earnings by Sector	United States	6/2/2017
            (state) Employment, Hours, and Earnings by Sector	Indiana	6/16/2017
            (state) Unemployment Rate	Indiana	6/16/2017
            (county) Unemployment Rate	Indiana MSAs	6/19/2017
            (msa) Unemployment Rate	Indiana MSAs	6/19/2017
            (msa) Employment by Sector	Indiana Counties	6/19/2017
            Producer Price Index	United States	7/13/2017
            Consumer Price Index	United States	7/14/2017
            Retail Sales	United States	7/17/2017
            Gross Domestic Product	United States	7/28/2017
            Durable Good Orders	United States	7/27/2017
            Motor Vehicle Retail Sales	United States	7/7/2017
            Industrial Production	United States	7/14/2017
            Housing Starts	United States	7/19/2017
            Unemployment Rates By State	United States	7/21/2017
            Manufacturing Employment By State	United States	7/21/2017
            Unemployment Rate	United States	7/7/2017
            Employment, Hours, and Earnings by Sector	United States	7/7/2017
            (state) Employment, Hours, and Earnings by Sector	Indiana	7/21/2017
            (state) Unemployment Rate	Indiana	7/21/2017
            (county) Unemployment Rate	Indiana MSAs	7/24/2017
            (msa) Unemployment Rate	Indiana MSAs	7/24/2017
            (msa) Employment by Sector	Indiana Counties	7/24/2017
            Producer Price Index	United States	8/10/2017
            Consumer Price Index	United States	8/11/2017
            Retail Sales	United States	8/15/2017
            Gross Domestic Product	United States	8/30/2017
            Durable Good Orders	United States	8/25/2017
            Motor Vehicle Retail Sales	United States	8/4/2017
            Industrial Production	United States	8/17/2017
            Housing Starts	United States	8/16/2017
            Unemployment Rates By State	United States	8/18/2017
            Manufacturing Employment By State	United States	8/18/2017
            Unemployment Rate	United States	8/4/2017
            Employment, Hours, and Earnings by Sector	United States	8/4/2017
            (state) Employment, Hours, and Earnings by Sector	Indiana	8/18/2017
            (state) Unemployment Rate	Indiana	8/18/2017
            (county) Unemployment Rate	Indiana MSAs	8/21/2017
            (msa) Unemployment Rate	Indiana MSAs	8/21/2017
            (msa) Employment by Sector	Indiana Counties	8/21/2017
            Producer Price Index	United States	9/13/2017
            Consumer Price Index	United States	9/14/2017
            Retail Sales	United States	9/15/2017
            Gross Domestic Product	United States	9/28/2017
            Durable Good Orders	United States	9/27/2017
            Personal Income	United States	9/26/2017
            Motor Vehicle Retail Sales	United States	9/1/2017
            Industrial Production	United States	9/15/2017
            Housing Starts	United States	9/19/2017
            Unemployment Rates By State	United States	9/15/2017
            Manufacturing Employment By State	United States	9/15/2017
            Unemployment Rate	United States	9/1/2017
            Employment, Hours, and Earnings by Sector	United States	9/1/2017
            (state) Employment, Hours, and Earnings by Sector	Indiana	9/15/2017
            (state) Unemployment Rate	Indiana	9/15/2017
            (county) Unemployment Rate	Indiana MSAs	9/18/2017
            (msa) Unemployment Rate	Indiana MSAs	9/18/2017
            (msa) Employment by Sector	Indiana Counties	9/18/2017
            Producer Price Index	United States	10/12/2017
            Consumer Price Index	United States	10/13/2017
            Retail Sales	United States	10/13/2017
            Gross Domestic Product	United States	10/27/2017
            Durable Good Orders	United States	10/25/2017
            Motor Vehicle Retail Sales	United States	10/6/2017
            Industrial Production	United States	10/17/2017
            Housing Starts	United States	10/18/2017
            Unemployment Rates By State	United States	10/20/2017
            Manufacturing Employment By State	United States	10/20/2017
            Unemployment Rate	United States	10/6/2017
            Employment, Hours, and Earnings by Sector	United States	10/6/2017
            (state) Employment, Hours, and Earnings by Sector	Indiana	10/20/2017
            (state) Unemployment Rate	Indiana	10/20/2017
            (county) Unemployment Rate	Indiana MSAs	10/23/2017
            (msa) Unemployment Rate	Indiana MSAs	10/23/2017
            (msa) Employment by Sector	Indiana Counties	10/23/2017
            Producer Price Index	United States	11/14/2017
            Consumer Price Index	United States	11/15/2017
            Retail Sales	United States	11/15/2017
            Gross Domestic Product	United States	11/29/2017
            Durable Good Orders	United States	11/22/2017
            Motor Vehicle Retail Sales	United States	11/3/2017
            Industrial Production	United States	11/16/2017
            Housing Starts	United States	11/17/2017
            Unemployment Rates By State	United States	11/17/2017
            Manufacturing Employment By State	United States	11/17/2017
            Unemployment Rate	United States	11/3/2017
            Employment, Hours, and Earnings by Sector	United States	11/3/2017
            (state) Employment, Hours, and Earnings by Sector	Indiana	11/17/2017
            (state) Unemployment Rate	Indiana	11/17/2017
            (county) Unemployment Rate	Indiana MSAs	11/20/2017
            (msa) Unemployment Rate	Indiana MSAs	11/20/2017
            (msa) Employment by Sector	Indiana Counties	11/20/2017
            Producer Price Index	United States	12/12/2017
            Consumer Price Index	United States	12/13/2017
            Retail Sales	United States	12/14/2017
            Gross Domestic Product	United States	12/21/2017
            Durable Good Orders	United States	12/22/2017
            Personal Income	United States	12/20/2017
            Motor Vehicle Retail Sales	United States	12/1/2017
            Industrial Production	United States	12/15/2017
            Housing Starts	United States	12/19/2017
            Unemployment Rates By State	United States	12/22/2017
            Manufacturing Employment By State	United States	12/22/2017
            Unemployment Rate	United States	12/8/2017
            Employment, Hours, and Earnings by Sector	United States	12/8/2017
            (state) Employment, Hours, and Earnings by Sector	Indiana	12/22/2017
            (state) Unemployment Rate	Indiana	12/22/2017
            (county) Unemployment Rate	Indiana MSAs	12/25/2017
            (msa) Unemployment Rate	Indiana MSAs	12/25/2017
            (msa) Employment by Sector	Indiana Counties	12/25/2017
	    ';
	    $lines = explode("\n", $data);

	    $this->loadModel('Category');
	    $categories = $this->Category->find('list');

        $this->loadModel('LocationType');
        $this->LocationType->displayField = 'name';
        $locationTypes = $this->LocationType->find('list');

	    foreach ($lines as $line) {
	        $line = trim($line);
	        if ($line == '') {
	            continue;
            }
            $split = explode("\t", $line);
            $category = $split[0];
            $locationTypeId = null;
            foreach ($locationTypes as $id => $locationType) {
                if (strpos($category, "($locationType) ") === 0) {
                    $locationTypeId = $id;
                    $category = substr($category, strlen($locationType) + 3);
                    continue;
                }
            }
            $date = $split[2];

            if ($locationTypeId) {
                $result = $this->Category->find('first', [
                    'conditions' => [
                        'Category.name' => $category,
                        'Category.location_type_id' => $locationTypeId
                    ]
                ]);
                $categoryId = $result['Category']['id'];
            } else {
                $categoryId = array_search($category, $categories);
            }
            if ($categoryId === false) {
                $this->Flash->error('The category "' . $category . '" is unrecognized"');
                continue;
            }

            $date = date('Y-m-d', strtotime($date));

            $this->Release->create();
            $releaseData = [
                'category_id' => $categoryId,
                'date' => $date
            ];
            $is_redundant = $this->Release->isScheduled($categoryId, [
                'year' => date('Y', strtotime($date)),
                'month' => date('m', strtotime($date)),
                'day' => date('d', strtotime($date))
            ]);

            // If this has already been entered, don't add another,
            // but still display a success message.
            if ($is_redundant) {
                $this->Flash->success('The release has already been saved');
            } elseif ($this->Release->save($releaseData)) {
                $this->Flash->success('The release has been saved');
            } else {
                $this->Flash->error('The release could not be saved.');
            }
        }
    }
}
