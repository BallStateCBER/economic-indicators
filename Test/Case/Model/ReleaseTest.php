<?php
App::uses('Release', 'Model');

/**
 * Release Test Case
 *
 */
class ReleaseTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.release', 'app.dataset', 'app.location', 'app.location_type', 'app.category', 'app.frequency', 'app.category_group');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Release = ClassRegistry::init('Release');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Release);

		parent::tearDown();
	}

}
