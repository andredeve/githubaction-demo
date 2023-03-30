<?php

namespace App\Enum;

/**
 * Classe TipoPessoa
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   09/01/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class SigiloProcesso {

    const SEM_RESTRICAO = 'sem-restricao';
    const RESTRICAO_PUBLICA = 'privado'; 
    const SIGILOSO = 'sigiloso';
    const ANEXOS_SIGILOSOS = 'anexos-sigilosos';

    public static function getOptions($option = false) {
        $options = array(
            self::SEM_RESTRICAO => 'Sem Restrição',
            self::SIGILOSO => 'Sigiloso',
            self::ANEXOS_SIGILOSOS => 'Anexos Sigilosos',
            self::RESTRICAO_PUBLICA => 'Restrição Pública'
        );
        
        if($option){
            return isset($options[$option])?$options[$option]:"";
        }
        return $options;
    }
    
    public static function getOptionsDescription(){
        return array(
            self::SEM_RESTRICAO => self::getOptions()[self::SEM_RESTRICAO]. ': o processo e seus anexos poderão ser vistos por todos os usuários.',
            self::SIGILOSO => self::getOptions()[self::SIGILOSO]. ': o processo só poderá ser visto pelo usuário responsável. Para visualizá-lo, será necessário fazer uma nova autenticação no sistema. ',
            self::ANEXOS_SIGILOSOS => self::getOptions()[self::ANEXOS_SIGILOSOS]. ':  o processo poderá ser visto por todos. Já os anexos só poderão ser visualizados pelo responsável do processo. ',
            self::RESTRICAO_PUBLICA => self::getOptions()[self::RESTRICAO_PUBLICA]. ':  o processo poderá ser visto por todos os usuários porem a consulta pública ficará indisponível. '
        );
    } 

}
