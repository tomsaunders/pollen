<?php
App::uses('Photo', 'Model');

/**
 * Photo Test Case
 *
 */
class PhotoTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	//public $fixtures = array('app.photo');
	
	
	private $paginate = [
		'limit' => 5,
		'conditions' => ['text' => 'Pollenizer']
	]; 

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Photo = ClassRegistry::init('Photo');
	}
	
	//check that the service is able to calculate a count of the results
	public function testCount(){
		$photoCount = $this->Photo->find('count');
		$this->assertEqual((int)$photoCount, $photoCount); //test photoCount is a valid integer		
	}
	
	//check that the service is returning the source URL for a thumbnail version of each photo
	public function testThumbnails(){
		$photos = $this->Photo->find('all', $this->paginate);
		foreach($photos as $photo){
			$this->assertArrayHasKey('thumbnail', $photo['Photo']);
		}
	}
	
	//check that the service is returning the source URL for a large version of each photo
	public function testLarge(){
		$photos = $this->Photo->find('all', $this->paginate);
		foreach($photos as $photo){
			$this->assertArrayHasKey('large', $photo['Photo']);
		}
	}
	
	//check that the service is returning the correct number of results when paginating
	public function testLimit(){
		$limit = rand(5, 100);
		$this->paginate['limit'] = $limit;
		$photos = $this->Photo->find('all', $this->paginate);
		$this->assertEquals($limit, count($photos));
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Photo);

		parent::tearDown();
	}

}
