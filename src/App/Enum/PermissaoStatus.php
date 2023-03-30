<?php

namespace App\Enum;

/**
 * Description of TipoLog
 *
 * @author ander
 */
abstract class PermissaoStatus {
    const OK = 1;
    const REQUER_MOTIVO = 2;
    const NEGADO = -1;
    const INDEFINIDO = 0;
}
