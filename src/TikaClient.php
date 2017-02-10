<?php

namespace polster\php\Tika\Client;

use Exception;
use polster\php\Tika\Client\AbstractTikaClient;

/**
 * Apache Tika Client implementation.
 * 
 * @author sdi
 * 
 * @link    http://wiki.apache.org/tika/TikaJAXRS
 */
class TikaClient extends AbstractTikaClient {
	
	/**
	 * Apache Tika server host.
	 *
	 * @var string
	 */
	private $host = '127.0.0.1';
		
	/**
	 * Apache Tika server port.
	 *
	 * @var int
	 */
	private $port = 9998;
	
	/**
	 * cURL options.
	 *
	 * @var array
	 */
	private $options =
	[
			CURLINFO_HEADER_OUT    => true,
			CURLOPT_PUT            => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 5,
	];
	
	/**
	 * Constructor.
	 *
	 * @param string $host
	 * @param int    $port
	 *
	 * @throws Exception
	 */
	public function __construct($host = null, $port = null, $options = []) {
		
		if ($host) {
			$this->host = $host;
		}
		if ($port) {
			$this->port = $port;
		}
		foreach ($options as $key => $value) {
			$this->options[$key] = $value;
		}
	}
	
	/**
	 * Returns the configured host.
	 *
	 * @return string
	 */
	public function getHost() {
		return $this->host;
	}
	
	/**
	 * Returns the configured port.
	 *
	 * @return null|int
	 */
	public function getPort() {
		return $this->port;
	}
	
	/**
	 * Returns the configured extra options.
	 *
	 * @return null|array
	 */
	public function getOptions() {
		return $this->options;
	}
	
	/**
	 * Configure and fire a request against the Tika Server and return the result.
	 *
	 * @param string $type
	 * @param string $file
	 *
	 * @return string
	 *
	 * @throws Exception
	 */
	protected function request($type, $file = null) {
		
		// parameters for cURL request
		$headers = [];
		switch ($type) {
			case 'html':
				$resource = 'tika';
				$headers[] = 'Accept: text/html';
				break;
			case 'text':
				$resource = 'tika';
				$headers[] = 'Accept: text/plain';
				break;
			case 'version':
				$resource = 'version';
				break;
			default:
				throw new Exception("Unknown type $type");
		}
		
		// base options
		$options = $this->options;
		
        if ($file && file_exists($file) && is_readable($file)) {
			// local file options
			$options[CURLOPT_INFILE] = fopen($file, 'r');
			$options[CURLOPT_INFILESIZE] = filesize($file);
		} elseif($type == 'version') {
			// other options for specific requests
			$options[CURLOPT_PUT] = false;
		} else {
			// error
			throw new Exception("File $file can't be opened");
		}
		
		// sets headers
		$options[CURLOPT_HTTPHEADER] = $headers;
		// cURL init and options
		$options[CURLOPT_URL] = "http://{$this->host}:{$this->port}" . "/$resource";
		
		// get the response and the HTTP status code
		list($response, $status) = $this->exec($options);
		
		if ($status != 200) {
			$this->error($status, $resource);
		}

		return $response;
	}
		
	/**
	 * Make a request to Apache Tika Server.
	 *
	 * @param array $options
	 *
	 * @return array
	 *
	 * @throws Exception
	 */
	private function exec(array $options = []) {
		
		// init cURL and configure
		$curl = curl_init();
		curl_setopt_array($curl, $options);
		
		// get response
		$response =
		[
				trim(curl_exec($curl)),
				curl_getinfo($curl, CURLINFO_HTTP_CODE),
		];

		if (curl_errno($curl)) {
			throw new Exception(curl_error($curl), curl_errno($curl));
		}
		
		
		return $response;
	}
	
	/**
	 * Throws an Exception reflecting the given status.
	 *
	 * @param int       $status
	 * @param string    $resource
	 *
	 * @throws Exception
	 */
	private function error($status, $resource) {
		
		switch ($status) {
			case 405:
				throw new Exception('Method not allowed', 405);
				break;
			case 422:
				throw new Exception('Unprocessable document', 422);
				break;
			case 500:
				throw new Exception('Error while processing document', 500);
				break;
			default:
				throw new Exception("Unexpected response for /$resource ($status)", 501);
		}
	}
}