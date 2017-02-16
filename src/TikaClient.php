<?php

namespace polster\php\Tika\Client;

use Exception;
use polster\php\Tika\Client\AbstractTikaClient;
use polster\php\Tika\Client\TikaClientException;

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
	public function __construct($host = null, $port = null) {
		
		if ($host) {
			$this->host = $host;
		}
		if ($port) {
			$this->port = $port;
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
     * Sets the host.
     *
     * @param string $host
     */
	public function setHost($host) {
	    $this->host = $host;
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
     * Sets the port.
     *
     * @param int $port
     */
	public function setPort($port) {
	    $this->port = $port;
    }

	/**
	 * Returns the configured extra options.
	 *
	 * @return array
	 */
	public function getOptions() {
		return $this->options;
	}

    /**
     * Returns the client connection timeout in seconds.
     *
     * @return int
     */
    public function getConnectionTimeout() {
        return $this->options[CURLOPT_TIMEOUT];
    }

    /**
     * Sets the connection timeout in seconds.
     *
     * @param $connectionTimeout
     * @throws TikaClientException
     */
    public function setConnectionTimeout($connectionTimeout) {

        if (!is_int($connectionTimeout) || $connectionTimeout < 0) {
            throw new TikaClientException("Given value must be a non negative integer!");
        }
        $this->options[CURLOPT_TIMEOUT] = $connectionTimeout;
    }

	/**
	 * Configure and fire a request against the Tika Server and return the result.
	 *
	 * @param string $type
	 * @param string $file
	 *
	 * @return string
	 *
	 * @throws TikaClientException
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
				throw new TikaClientException("Unknown type $type");
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
			throw new TikaClientException("File $file can't be opened");
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
	 * @throws TikaClientException
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
			throw new TikaClientException(curl_error($curl), curl_errno($curl));
		}

		return $response;
	}
	
	/**
	 * Throws a TikaClientException reflecting the given status.
	 *
	 * @param int       $status
	 * @param string    $resource
	 *
	 * @throws TikaClientException
	 */
	private function error($status, $resource) {
		
		switch ($status) {
			case 405:
				throw new TikaClientException('Method not allowed', 405);
				break;
			case 422:
				throw new TikaClientException('Unprocessable document', 422);
				break;
			case 500:
				throw new TikaClientException('Error while processing document', 500);
				break;
			default:
				throw new TikaClientException("Unexpected response for /$resource ($status)", 501);
		}
	}
}