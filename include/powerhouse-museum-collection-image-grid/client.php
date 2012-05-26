<?php
	class PowerhouseAPI{
		private $version  	 = 'v1';
		private $endpoint_URL = 'http://api.powerhousemuseum.com/api';
		private $format      = 'json';
		private $api_key	 = '';
		private $cache		 = null;

		public function __construct($api_key, $version=Null) {
			$this->version = (is_null($version))?$this->version:$version;
			$this->api_key = $api_key;

		}

		public function enable_cache($cache_path=Null){
			if($cache_path){
				$this->cache = new JG_Cache($cache_path);
			}
		}


		private function get_options(){
			$options = array();
		   	$options[CURLOPT_RETURNTRANSFER] =  true;
		   	$options[CURLOPT_HEADER]         =  false;
		   	$options[CURLOPT_FOLLOWLOCATION] =  true;
		   	$options[CURLOPT_ENCODING]       =  '';
		   	$options[CURLOPT_USERAGENT]      =  'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322)';
		   	$options[CURLOPT_AUTOREFERER]    =  true;
		   	$options[CURLOPT_CONNECTTIMEOUT] =  1200;
		   	$options[CURLOPT_TIMEOUT]        =  1200;
		   	$options[CURLOPT_MAXREDIRS]      =  10;
		   	$options[CURLOPT_SSL_VERIFYPEER] =  false;

			return $options;

		}

		private function build_call_URL($resource_path){
			$url = sprintf('%s/%s/%s/%s/', $this->endpoint_URL,
													  $this->version,
													  $resource_path,
													  $this->format);
			return $url;
		}


		private function call($url, $arguments=array(), $expire_cache=Null){

			//Build parameters
			$options = $this->get_options();
			$urlVar  = array();

			if($arguments){
				foreach ($arguments as $key => $value) {
    				$urlVar[] = $key.'='.rawurlencode(trim($value));
				}
			}

			$options[CURLOPT_HTTPGET]  =  true;
			if(count($urlVar)>0){
				$urlVariables = implode("&",$urlVar);
				$url = $url.'?'.$urlVariables;
			}

			//Check cache before to this call
			$key = md5($url);
			$result = $this->get_cache($key, $expire_cache);
			if($result === False){
				$ch     = curl_init( $url );
				curl_setopt_array($ch, $options);
				$result = curl_exec( $ch );
				curl_close( $ch );

				$this->set_cache($key, $result);
			}

			return $result;
		}

		private function get_cache($key, $expire_cache=Null){
			$result = False;
			if(!is_null($this->cache)){
				$expire_cache = (is_null($expire_cache))?3600:$expire_cache;
				$result =  $this->cache->get($key, $expire_cache);
			}
			return $result;
		}

		private function set_cache($key, $data){
			if(!is_null($this->cache)){
				$this->cache->set($key, $data);
			}
			return $data;
		}


		public function do_request($resource_path, $arguments = array(), $expire_cache=Null){
			$url = $this->build_call_URL($resource_path);

			$authentication = array();
			$authentication['api_key'] = $this->api_key;
			$arguments = array_merge($authentication, $arguments);

			$result = $this->call($url, $arguments, $expire_cache);

			if ($this->format == 'json'){
				$result = json_decode($result);
			}

			return $result;

		}


	}

	/**
	 * From more information go to the following link
	 * http://www.jongales.com/blog/2009/02/18/simple-file-based-php-cache-class/
	 */

	class JG_Cache {

	    function __construct($dir){
	        $this->dir = $dir;
	    }

	    private function _name($key){
	        return sprintf("%s/%s", $this->dir, sha1($key));
	    }

	    public function get($key, $expiration = 3600){

	        if ( !is_dir($this->dir) OR !is_writable($this->dir)){
	            return FALSE;
	        }

	        $cache_path = $this->_name($key);

	        if (!@file_exists($cache_path)){
	            return FALSE;
	        }

	        if (filemtime($cache_path) < (time() - $expiration)){
	            $this->clear($key);
	            return FALSE;
	        }

	        if (!$fp = @fopen($cache_path, 'rb')){
	            return FALSE;
	        }

	        flock($fp, LOCK_SH);

	        $cache = '';

	        if (filesize($cache_path) > 0){
	            $cache = unserialize(fread($fp, filesize($cache_path)));
	        }else{
	            $cache = NULL;
	        }

	        flock($fp, LOCK_UN);
	        fclose($fp);

	        return $cache;
	    }

	    public function set($key, $data){

	        if ( !is_dir($this->dir) OR !is_writable($this->dir)){
	            return FALSE;
	        }

	        $cache_path = $this->_name($key);

	        if ( ! $fp = fopen($cache_path, 'wb')){
	            return FALSE;
	        }

	        if (flock($fp, LOCK_EX)){
	            fwrite($fp, serialize($data));
	            flock($fp, LOCK_UN);
	        }else{
	            return FALSE;
	        }
	        fclose($fp);
	        @chmod($cache_path, 0777);
	        return TRUE;
	    }

	    public function clear($key){
	        $cache_path = $this->_name($key);

	        if (file_exists($cache_path)){
	            unlink($cache_path);
	            return TRUE;
	        }

	        return FALSE;
	    }
	}



?>