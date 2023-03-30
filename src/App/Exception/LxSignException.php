<?php

namespace App\Exception;

use Core\Exception\TechnicalException;

class LxSignException extends TechnicalException
{
    public function __construct($message, $e = null)
    {
        parent::__construct($message, $e);
    }
}