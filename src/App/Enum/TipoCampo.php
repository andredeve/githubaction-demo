<?php

namespace App\Enum;

/**
 * Classe TipoCampo
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   30/01/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class TipoCampo {

    const TEXTO = 'texto';
    const CAIXA_TEXTO = 'caixa-texto';
    const CAIXA_SELECAO = 'caixa-selecao';
    const NUMERO = 'numero';
    const DATA = 'data';
    const HORA = 'hora';
    const EMAIL = 'email';
    const ARQUIVO = 'arquivo';
    const ARQUIVO_MULTIPLO = 'arquivo-multiplo';
    const PROCESSO = 'processo';

    public static function getOptions() {
        return array(
            self::TEXTO => 'Texto',
            self::CAIXA_TEXTO => 'Caixa de Texto',
            self::CAIXA_SELECAO => 'Caixa de Seleção',
            self::NUMERO => 'Número',
            self::DATA => 'Data',
            self::HORA => 'Hora',
            self::EMAIL => 'E-mail',
            self::ARQUIVO => 'Arquivo', 
            // Com as mudanças feitas para Três Lagoas, o campo de Arquivo já faz o tratamento de vários arquivos, tornando
            // desnecessário esse tipo de campo.
            //self::ARQUIVO_MULTIPLO => 'Arquivo Múltiplo',
            self::PROCESSO => \App\Controller\IndexController::getParametosConfig()['nomenclatura']  
            
        );
    }

    public static function getDescricao($constante) {
        return self::getOptions()[$constante];
    }

}
