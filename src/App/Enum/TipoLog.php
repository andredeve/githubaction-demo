<?php

namespace App\Enum;

/**
 * Description of TipoLog
 *
 * @author ander
 */
abstract class TipoLog {

    const LOGIN_SUCCESS = 'login-success';
    const LOGIN_ATTEMPT = 'login-attempt';
    const ACTION_INSERT = 'insert';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';

}
