<?php

namespace App\Enum;

/**
 * Classe TipoUsuario
 * @version 1.0
 * @author Anderson Brandão <batistoti@gmail.com>
 * 
 * @copyright 2016 Lxtec Informática LTDA
 */
abstract class TipoUsuario {

    const ADMINISTRADOR = 'admin';
    const USUARIO = 'usuario';
    const MASTER = 'master';
    const INTERESSADO = 'interessado';
    const VISITANTE = 'visitante';

    public static function getOptions() {
        return array(
            self::ADMINISTRADOR => 'Administrador',
            self::USUARIO => 'Usuário',
            self::MASTER => 'Master',
            self::INTERESSADO => 'Interessado',
            self::VISITANTE => 'Visitante'
        );
    }

    public static function getTipos() {
        return array(
            self::ADMINISTRADOR,
            self::USUARIO,
            self::MASTER,
            self::INTERESSADO,
            self::VISITANTE
        );
    }

    public static function getDescricao($constante) {
        return self::getOptions()[$constante];
    }

}
