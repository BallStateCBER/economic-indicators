<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->deny('add', 'edit', 'delete', 'view', 'index', 'my_account');
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Flash->success('New user added.');
				$this->redirect('/');
			} else {
				$this->Flash->error('The user could not be saved. Please, try again.');
			}
		}
		$this->set('title_for_layout', 'Add User');
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
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
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->User->delete()) {
			$this->Session->setFlash(__('User deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
	
	public function login() {
	    if ($this->request->is('post')) {
	        if ($this->Auth->login()) {
	        	$user = $this->Auth->user();
	            $this->redirect($this->Auth->redirect());
	        } else {
	            $this->Flash->error('Your email address or password was incorrect.');
	        }
	    }
	    $this->set('title_for_layout', 'Login');
	}
	
	
	
	public function logout() {
		$this->Flash->success('You are now logged out.');
		$this->redirect($this->Auth->logout());
	}
	
	public function my_account() {
		$password_changed = false;
		$this->User->id = $this->Auth->user('id');
		if (empty($this->request->data)) {
			$this->request->data = $this->User->read();
		} else {
			// Unless if both password fields have values, unset them so they don't go through validation
			if ($this->request->data['User']['new_password'] == '' || $this->request->data['User']['confirm_password'] == '') {
				if ($this->request->data['User']['new_password'] != '') {
					$no_password_confirmation = true;
				}
				unset($this->request->data['User']['new_password']);
				unset($this->request->data['User']['confirm_password']);
			} elseif ($this->request->data['User']['new_password'] == $this->request->data['User']['confirm_password']) {
				$this->request->data['User']['password'] = $this->request->data['User']['new_password'];
				$password_changed = true;
			} else {
				$password_mismatch = true;
				// $this->User->validationErrors['new_password'] must be set 
				// after $this->User->validates() 
			}
			$this->User->set($this->request->data);
			if ($this->User->validates()) {
				// Force lowercase email
				$this->request->data['User']['email'] = strtolower(trim($this->request->data['User']['email']));
				if ($this->User->save()) {
					$this->Flash->success('Your account has been updated.');
					if ($password_changed) {
						$this->Flash->success('Your password has been changed.');
						
						// Unset passwords so those fields aren't auto-populated
						unset($this->request->data['User']['new_password']);
						unset($this->request->data['User']['confirm_password']);
					}
				} else {
					$this->Flash->error('Error updating account.');
				}
			}
		}
		if (isset($password_mismatch)) {
			$this->User->validationErrors['confirm_password'] = "Your passwords did not match.";	
		}
		if (isset($no_password_confirmation)) {
			$this->User->validationErrors['confirm_password'] = "You must also type in your new password here to confirm it.";
		}
		$this->set(array(
			'title_for_layout' => 'My Account'
		));
	}
}
