<?php

namespace App\Enum;

/**
 * Classe TipoTemporalidade
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   19/11/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class TipoTemporalidade {

    const ANOS = 'anos';
    const MESES = 'meses';
    const DIAS = 'dias';

    public static function getOptions() {
        return array(
            self::ANOS => 'anos',
            self::MESES => 'meses',
            self::DIAS => 'dias'
        );
    }

    public static function getDescricao($constante) {
        if (!empty($constante)) {
            return self::getOptions()[$constante];
        }
        return null;
    }

}
