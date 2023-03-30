<?php
namespace Core\Exception;

use PhpOffice\PhpWord\Exception\Exception;

class TechnicalException extends AppException {

    /**
     * @param string $message
     * @param Exception|null $e
     * @param int|null $code
     */
    public function __construct($message, $e = null, $code = null)
    {
        parent::__construct($message, $e, $code);
    }
}