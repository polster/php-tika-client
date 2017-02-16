<?php

namespace polster\php\Tika\Client;

/**
 * Apache Tika Client interface.
 * 
 * @author sdi
 * 
 * @link    http://wiki.apache.org/tika/TikaJAXRS
 */
abstract class AbstractTikaClient {
	
	/**
	 * Extracts text.
	 *
	 * @param string $file
	 *
	 * @return string
	 *
	 * @throws \TikaClientException
	 */
	public function getText($file) {
		return $this->request('text', $file);
	}
	
	/**
	 * Extracts HTML.
	 *
	 * @param string $file
	 *
	 * @return string
	 *
	 * @throws \TikaClientException
	 */
	public function getHTML($file) {
		return $this->request('html', $file);
	}
	
	/**
	 * Configure and fire a request against the Tika Server and return the result.
	 *
	 * @param string $type
	 * @param string $file
	 *
	 * @return string
	 *
	 * @throws \TikaClientException
	 */
	abstract protected function request($type, $file);
	
	/**
	 * Returns the current Apache Tika version.
	 *
	 * @return string
	 */
	public function getVersion() {
		return $this->request('version');
	}
}