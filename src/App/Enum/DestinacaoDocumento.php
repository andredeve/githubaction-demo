<?php

namespace App\Enum;

use Core\Model\AppModel;

/**
 * Classe DestinacaoDocumento
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   19/11/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class DestinacaoDocumento {

    const PERMANENTE = 'permanente';
    const ELIMINACAO = 'eliminacao';

    public static function getOptions() {
        return array(
            self::PERMANENTE => 'Guarda Permanente',
            self::ELIMINACAO => 'Eliminação'
        );
    }

    public static function getDescricao($constante) {
        if (!empty($constante)) {
            return self::getOptions()[$constante];
        }
        return null;
    }
}
