<?php
namespace Core\Exception;
use Core\Enum\TipoMensagem;

use Exception;

/**
 * Exceções geradas pela invalidação de regras de negócios.
 *
 */
class BusinessException extends AppException {

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
