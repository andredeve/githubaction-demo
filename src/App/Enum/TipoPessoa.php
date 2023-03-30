<?php

namespace App\Enum;

/**
 * Classe TipoPessoa
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   09/01/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class TipoPessoa {

    const FISICA = 'fisica';
    const JURIDICA = 'juridica';

    public static function getOptions() {
        return array(
            self::FISICA => 'Física',
            self::JURIDICA => 'Jurídica',
        );
    }

}
