<?php

namespace App\Enum;

/**
 * Classe TipoHistoricoProcesso
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   02/02/2018
 * @copyright (c) 2018, Lxtec Informática
 */
abstract class TipoHistoricoAnexo
{
    const INSERT = 'insert';
    const UPDATE = 'update';
    const DELETE = 'delete';
    const LOG = 'log';
    const LOGIN_ATTEMPT = 'login-attempt';
    const LOGIN_SUCCESS = 'login-success';
}
