<?php

namespace App\Enum;

/**
 * Classe EstadoCivil
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   23/10/2017
 * @copyright (c) 2017, Lxtec Informática
 */
abstract class EstadoCivil {

    const SOLTEIRO = 'solteiro';
    const CASADO = 'casado';
    const SEPARADO = 'separado';
    const DIVORCIADO = 'divorciado';
    const VIUVO = 'viuvo';

    public static function getOptions() {
        return array(
            self::SOLTEIRO => 'Solteiro(a)',
            self::CASADO => 'Casado(a)',
            self::SEPARADO => 'Separado(a)',
            self::DIVORCIADO => 'Divorciado(a)',
            self::VIUVO => 'Viúvo(a)',
        );
    }

}
