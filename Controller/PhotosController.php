<?php
App::uses('AppController', 'Controller');
/**
 * Photos Controller
 *
 * @property Photo $Photo
 */
class PhotosController extends AppController {
	
	protected $paginate = [
		'limit' => 5,
		'conditions' => ['text' => 'Pollenizer']
	]; 


/**
 * index method
 *
 * @return void
 */
	public function index() {
		if($this->data){
			$this->paginate['conditions']['text'] = $this->data['search'];
			$this->Session->write('search', $this->data['search']);
		}
		else if($this->Session->check('search')){
			$this->paginate['conditions']['text'] = $this->Session->read('search');
		}
		$this->request->data['search'] = $this->paginate['conditions']['text'];
		$this->Photo->recursive = 0;
		$this->set('photos', $this->paginate());
		$this->set('print', $this->Photo->find('count'));
		
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Photo->id = $id;
		if (!$this->Photo->exists()) {
			throw new NotFoundException(__('Invalid photo'));
		}
		$this->set('photo', $this->Photo->read(null, $id));
	}
}
