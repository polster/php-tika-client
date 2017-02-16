<?php

namespace polster\php\Tika\Client;

use Exception;

/**
 * Tika Client specific exception.
 */
class TikaClientException extends Exception {

    /**
     * Constructor.
     *
     * @param string $message
     * @param int $code
     */
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }
}