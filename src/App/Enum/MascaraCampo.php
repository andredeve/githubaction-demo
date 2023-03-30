<?php

namespace App\Enum;

/**
 * Classe MascaCampo
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   30/01/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class MascaraCampo {

    const CPF = 'cpf';
    const CNPJ = 'cnpj';
    const TELEFONE = 'telefone';
    const DATA = 'data';
    const HORA = 'hora';
    const MOEDA = 'moeda';
    const CEP = 'cep';
    const EMAIL = 'email';

    public static function getOptions() {
        return array(
            array('value' => self::CPF, 'text' => 'C.P.F.', 'class' => TipoCampo::TEXTO),
            array('value' => self::CNPJ, 'text' => 'C.N.P.J.', 'class' => TipoCampo::TEXTO),
            array('value' => self::TELEFONE, 'text' => 'Telefone', 'class' => TipoCampo::TEXTO),
            array('value' => self::CEP, 'text' => 'C.E.P.', 'class' => TipoCampo::TEXTO),
            array('value' => self::DATA, 'text' => 'Data', 'class' => TipoCampo::DATA),
            array('value' => self::HORA, 'text' => 'Hora', 'class' => TipoCampo::HORA),
            array('value' => self::MOEDA, 'text' => 'Moeda', 'class' => TipoCampo::NUMERO),
            array('value' => self::EMAIL, 'text' => 'E-mail', 'class' => TipoCampo::EMAIL)
        );
    }

    public static function getDescricao($constante) {
        return self::getOptions()[$constante];
    }

}
