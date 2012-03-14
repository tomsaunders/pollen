<?php
/**
 * Flickr DataSource
 * 
 * based on the Twitter datasource example
 */
App::uses('HttpSocket', 'Network/Http');

class FlickrSource extends DataSource {
	protected $_schema = [
		'photos' => [
			'id' => 	['type' => 'integer', 'null' => true, 'key' => 'primary', 'length' => 11],
			'owner' =>  ['type' => 'string', 'null' => true, 'length' => 20],
			'secret' =>  ['type' => 'string', 'null' => true, 'length' => 20],
			'server' =>  ['type' => 'integer', 'null' => true, 'length' => 11],
			'farm' =>  ['type' => 'integer', 'null' => true, 'length' => 11],
			'title' =>  ['type' => 'string', 'null' => true, 'length' => 255],
			'ispublic' =>  ['type' => 'integer', 'null' => true, 'length' => 1],
			'isfriend' =>  ['type' => 'integer', 'null' => true, 'length' => 1],
			'isfamily' =>  ['type' => 'integer', 'null' => true, 'length' => 1]
		]
	];
	
	private $searchParams = [
		'method' => 'flickr.photos.search',
		'extras' => '',
		'format' => 'json',
		'nojsoncallback' => 1,
		'text' => 'Pollenizer'
	];
	
	private $flickr_uri = "http://api.flickr.com/services/rest/";
	
	public function __construct($config){
		$this->connection = new HttpSocket();
		$this->searchParams['api_key'] = $config['key'];
		parent::__construct($config);
	}
	
	public function listSources($data = NULL){
		return ['photos'];
	}
	
	public function calculate(&$model, $func, $params = array()){
		//$this->log(print_r([$func, $params], true), 'debug');
		return '__'.$func;
	}
	
	public function expression($expression){
		return $expression;
	}
	
	public function read(Model $model, $queryData = array()){
		if(isset($queryData['limit'])) $this->searchParams['per_page'] = $queryData['limit'];
		if(isset($queryData['page'])) $this->searchParams['page'] = $queryData['page'];
		if(isset($queryData['conditions']['text'])) $this->searchParams['text'] = $queryData['conditions']['text'];
				
		$key = md5(serialize($this->searchParams));
		$response = Cache::read($key);
		if($response === false){
			$response = json_decode($this->connection->get($this->flickr_uri, $this->searchParams), true)['photos'];
			Cache::write($key, $response);	
		}
		
		if($queryData['fields'] == '__count'){
			return [
				'0' => ['Photo' => ['count' => $response['total']]]
			];
		}
		
		$results = array();
		foreach($response['photo'] as $photo){
			$photo['thumbnail'] = $this->__getPhotoSourceURL($photo, 't');
			$photo['large'] = $this->__getPhotoSourceURL($photo, 'b');
			$results[] = ['Photo' => $photo];
		}
		
		$this->log('returning photos', 'debug');
		return $results;
		//http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=e2c5e3ec41c803918d6d3ed7bbf303b2&text=pollen&extras=&format=json&nojsoncallback=1
	}
	
	/**
	 * Based on info from http://www.flickr.com/services/api/misc.urls.html
	 * Gets the source URL to a photo once you know its ID, server ID, farm ID and secret
	 * @param $photo array of information returned from API call (has id, server, farm, secret)
	 * @param $type size suffix - 
	 * 	t thumbnail 
	 * 	b large
	 */
	private function __getPhotoSourceURL($photo, $type){
		return "http://farm{$photo['farm']}.staticflickr.com/{$photo['server']}/{$photo['id']}_{$photo['secret']}_$type.jpg";
	}
	
	public function isConnected(){
		return $this->connected;
	}
	
	public function describe($model){
		return $this->_schema['photos'];
	}
	
}