<?php

namespace Core\Exception;

use Exception;

abstract class AppException extends Exception {

    /**
     * @param string $message
     * @param Exception|null $e
     * @param int|null $code
     */
    public function __construct($message, $e, $code = null)
    {
        parent::__construct($message, $code, $e);
    }
}
