<?php
namespace Core\Exception;

use Exception;

/**
 * Exceções geradas pela invalidação de regras de segurança do negócio.
 *
 */
class SecurityException extends AppException {

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