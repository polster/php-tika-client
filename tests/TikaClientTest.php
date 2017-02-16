<?php

namespace polster\php\Tika\Tests;

use polster\php\Tika\Client\TikaClient;
use polster\php\Tika\Client\TikaClientException;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the TikaClient.
 * 
 * @author sdi
 *
 */
class TikaClientTest extends TestCase {
	
	// subject to be tested
	private $client;
	
	private $testFilePath;
	
	/**
	 * Setup.
	 */
	protected function setup() {
		
		$this->client = new TikaClient();
		$this->testFilePath = dirname(__DIR__) . "/tests/test-files";
	}

    /**
     * See method name.
     */
	public function testShouldSetConnectionTimeout() {

	    // given
        $client = new TikaClient();

        // when
        $client->setConnectionTimeout(300);

        // then
        $this->assertEquals(300, $client->getConnectionTimeout());
    }

    /**
     * See method name.
     */
    public function testShouldSetConnectionTimeoutThrowExceptionIfStringValue() {

        // given
        $client = new TikaClient();

        try {

            // when
            $client->setConnectionTimeout("bad value");

        } catch (TikaClientException $e) {

            // then
            $this->assertTrue(true);
        }
        $this->assertFalse(false, "Exception expected!");

    }

    /**
     * See method name.
     */
    public function testShouldSetConnectionTimeoutThrowExceptionIfNegativeValue() {

        // given
        $client = new TikaClient();

        try {

            // when
            $client->setConnectionTimeout(-34);

        } catch (TikaClientException $e) {

            // then
            $this->assertTrue(true);
        }
        $this->assertFalse(false, "Exception expected!");

    }

	/**
	 * See method name.
	 */
	public function testShouldReturnVersion() {
		
		// given
		// test client
		
		// when
		$version = $this->client->getVersion();
		
		// then
		$this->assertStringStartsWith("Apache Tika ", $version);
	}
	
	/**
	 * See method name.
	 */
	public function testShouldGetHtmlText() {
		
		// given
		$file = $this->testFilePath . "/test1.html";
		
		// when
		$text = $this->client->getHTML($file);
		
		// then
		$this->assertContains("STAR WARS", $text);
	}

	/**
	 * See method name.
	 */
	public function testShouldGetDocxText() {
	
		// given
		$file = $this->testFilePath . "/test2.docx";
	
		// when
		$text = $this->client->getText($file);
	
		// then
		$this->assertContains("Star Trek", $text);
	}
	
	/**
	 * See method name.
	 */
	public function testShouldGetPdfText() {
	
		// given
		$file = $this->testFilePath . "/test3.pdf";
	
		// when
		$text = $this->client->getText($file);
	
		// then
		$this->assertContains("STAR	WARS", $text);
	}

	/**
	 * See method name.
	 */
	public function testShouldGetHost() {
		
		// given
		$client = new TikaClient("tika-server.example.com", 9999);
		
		// when
		$host = $client->getHost();
		
		// then
		$this->assertEquals("tika-server.example.com", $host);
	}
	
	/**
	 * See method name.
	 */
	public function testShouldGetPort() {
	
		// given
		$client = new TikaClient("tika-server.example.com", 9999);
	
		// when
		$port = $client->getPort();
	
		// then
		$this->assertEquals(9999, $port);
	}
	
	/**
	 * See method name.
	 */
	public function testShouldThrowExceptionIfNoFile() {
		
		// given
		$file = $this->testFilePath . "/file-not-present.doc";

		try {

            // when
            $this->client->getText($file);

        } catch (TikaClientException $e) {

		    // then
            $this->assertTrue(true);
        }
        $this->assertFalse(false, "Exception expected!");
	}
	
	/**
	 * See method name.
	 */
	public function testShouldThrowExceptionIfUnresolvableHost() {
	
		// given
		$this->client = new TikaClient("unknown-tika-server");
		$file = $this->testFilePath . "/test3.pdf";

		try {

            // when
            $this->client->getText($file);

        } catch (TikaClientException $e) {

            // then
            $this->assertTrue(true);
        }
        $this->assertFalse(false, "Exception expected!");
	}

}