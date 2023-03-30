<?php

namespace App\Enum;

/**
 * Description of Sexo
 *
 * @author ander
 */
abstract class Sexo {

    const MASCULINO = 'm';
    const FEMININO = 'f';

    public static function getOptions() {
        return array(
            self::MASCULINO => 'Masculino',
            self::FEMININO => 'Feminino',
        );
    }

}
