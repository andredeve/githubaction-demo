<?php

include_once '../../../bootstrap.php';

use App\Controller\UsuarioController;
use App\Model\PermissaoEntidade;

$usuario_logado = UsuarioController::getUsuarioLogadoDoctrine();
if ($usuario_logado->getTipo() != \App\Enum\TipoUsuario::MASTER) {
    $entidade = filter_input(INPUT_POST, 'entidade');
    $acao = filter_input(INPUT_POST, 'acao');
    $permissao = $usuario_logado->getPermissoesEntidade(PermissaoEntidade::getCodigo($entidade));
    $method = 'getInserir';
    switch ($acao) {
        case 'inserir':
            $method = 'getInserir';
            break;
        case 'editar':
            $method = 'getEditar';
            break;
        case 'excluir':
            $method = 'getExcluir';
            break;
    }
    echo $prosseguir = $permissao instanceof PermissaoEntidade ? $permissao->$method() : true;
} else {
    echo true;
}

