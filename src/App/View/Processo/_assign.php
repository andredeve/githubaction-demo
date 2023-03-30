<?php

use App\Controller\IndexController;
use App\Controller\UsuarioController;
use App\Controller\InteressadoController;
use App\Model\Assunto;
use App\Model\Classificacao;
use App\Model\Interessado;
use App\Model\Processo;
use App\Model\TipoAnexo;
use App\Enum\TipoUsuario;
use App\Enum\OrigemProcesso;

$usuario_logado = UsuarioController::getUsuarioLogadoDoctrine();
$usuarioEhInteressado = $usuario_logado->isInteressado();

$smarty->assign("usuario_logado", $usuario_logado);
if($usuario_logado->getTipo() == TipoUsuario::INTERESSADO){
    $smarty->assign("assuntos", Assunto::getAssuntosExternos());
}else{
    $smarty->assign("setores", $usuario_logado->getSetores());
    $tiposPermitidos = array(TipoUsuario::ADMINISTRADOR,TipoUsuario::MASTER );
    $podeAlterarNumeroProcesso = in_array($usuario_logado->getTipo(), $tiposPermitidos);
}
$smarty->assign('usuarioEhInteressado', $usuarioEhInteressado);
$smarty->assign("podeAlterarNumeroProcesso", (!$usuarioEhInteressado) ? $podeAlterarNumeroProcesso : false );
$smarty->assign("tipos_documento", (new TipoAnexo())->listar());
$smarty->assign('origens', OrigemProcesso::getOptions());
$smarty->assign('config', IndexController::getConfig());