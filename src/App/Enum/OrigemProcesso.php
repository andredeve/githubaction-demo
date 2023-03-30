<?php

namespace App\Enum;

use ReflectionClass;

/**
 * Classe OrigemProcesso
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   16/01/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class OrigemProcesso {

    //'sistema','interna', 'externa','email','telefone','outros'
    const SISTEMA = 'sistema';
    const INTERNA = 'interna';
    const EXTERNA = 'externa';
    const EMAIL = 'email';
    const TELEFONE = 'telefone';
    const OUTROS = 'outros';

    public static function getOptions() {
        return array(
            self::INTERNA => 'Interna',
            self::EXTERNA => 'Externa',
            self::EMAIL => 'E-mail',
            self::TELEFONE => 'Telefone',
            self::OUTROS => 'Outros',
        );
    }

    public static function getDescricao($constante) {
        return self::getOptions()[$constante];
    }

}
