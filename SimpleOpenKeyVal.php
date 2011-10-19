<?php
/**
* A simple PHP library to interface with OpenKeyval.org
*
* OpenKeyval.org is a service for easily storing and retrieving key/value pairs
* via HTTP. SimpleOpenKeyVal is a PHP library to interface with this service.
* 
* Website: http://github.com/mohitsoni/SimpleOpenKeyVal
*
* @author     Mohit Soni (mohitsoni1989@gmail.com)
* @version    0.1
* 
*/
class SimpleOpenKeyVal {
	private $API_URL = 'http://api.openkeyval.org/';
	private $cacheTime = NULL;
	private $CACHE = array();

	public function __construct($cacheTime=NULL, $ssl=NULL) {
		$this->cacheTime = $cacheTime;
		if (isset($ssl)) {
			$API_URL = 'http://api.openkeyval.org/';
		}
	}

	public function get($key) {
		$this->expireCache();
		if (isset ($this->cacheTime)) {
			if (in_array($key, $this->CACHE)) {
				return $this->CACHE[$key][0];
			}
		}
		
		$res = $this->execGET($this->API_URL . $key);
		$this->CACHE[$key] = array($res, time());
		return $res;
	}

	public function set($key, $val) {
		$this->expireCache();
		$res = $this->store($key, $val);

		if (strcmp($res->status, 'multiset') === 0) {
    		$this->CACHE[$key] = array($val, time());
    	}

    	return $res;
	}

    public function del($key) {
    	$this->removeFromCache($key);
    	$res = $this->store($key, '');

    	return $res;
    }

    public function clearCache() {
    	$this->CACHE = array();
    }

    public function keyInfo($key) {
    	$res = $this->execGET($this->API_URL . $key . '?key_info');
    	return json_decode($res);
    }    

	private function store($key, $val) {
    	$this->removeFromCache($key);    	
    	$data = array($key => $val);    	
    	$res = $this->execPOST($this->API_URL, $data);
    	
    	return json_decode($res);
    }

    public function updateCache($key, $val) {
    	if (isset($this->cacheTime)) {
    		$this->CACHE[$key] = array($val, time());
    	}
    }

    public function removeFromCache($key) {
    	if (isset($this->cacheTime) && in_array($key, $this->CACHE)) {
    		unset($this->CACHE[$key]);
    	}
    }

    public function expireCache() {
    	if (isset($this->cacheTime)) {
    		$now = time();
    		foreach ($this->CACHE as $key=>$val) {
    			if ($now - $this->CACHE[$key][1] > $this->cacheTime) {    				
    				unset($this->CACHE[$key]);
    			}
    		}
    	}
    }

	public function cacheContains($key) {
    	if (isset($this->CACHE[$key])) {    		
    		return true;
    	} else {
    		return false;
    	}
    }

    private function execGET($url) {		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		
		$output = curl_exec($ch);

		if ($output === FALSE) {
			echo 'cURL error: ' . curl_error($ch);
		}

		curl_close($ch);

		return $output;
    }

    private function execPOST($url, $data) {
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		$output = curl_exec($ch);

		if ($output === FALSE) {
			echo 'cURL error: ' . curl_error($ch);
		}

		curl_close($ch);

		return $output;
    }
}
?>