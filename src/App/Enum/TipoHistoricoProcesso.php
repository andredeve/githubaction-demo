<?php

namespace App\Enum;

/**
 * Classe TipoHistoricoProcesso
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   02/02/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class TipoHistoricoProcesso {

    const CRIADO = 'criado';
    const ATUALIZADO = 'atualizado';
    const RECEBIDO = 'recebido';
    const ENVIADO = 'enviado';
    const NOVO_ANEXO = 'novo-anexo';
    const CANCELADO_ENVIO = 'cancelado-envio';
    const VISUALIZADO = 'visualizado';
    const ARQUIVADO = 'arquivado';
    const EMAIL_ENVIADO = 'email-enviado';
    const EMAIL_ERRO = 'email-erro';

}
