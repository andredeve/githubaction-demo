<?php

use App\Model\HistoricoProcesso;
use App\Model\Usuario;
use App\Controller\UsuarioController;
use App\Enum\TipoUsuario;
use App\Model\StatusProcesso;

die('Teste');

$smarty->assign('page_title', 'Início');
$smarty->assign('page_icon', 'fa fa-home');
$usuario_logado = UsuarioController::getUsuarioLogadoDoctrine();
$smarty->assign("usuario_logado", $usuario_logado);
if($usuario_logado->getTipo() == TipoUsuario::INTERESSADO){
    $smarty->assign('status_processo', (new StatusProcesso())->listarAtivos());
}else{
    $smarty->assign("usuarios", (new Usuario())->listarUsuarios());
    $smarty->assign("movimentacoes", (new HistoricoProcesso())->listarProximos());
    $smarty->assign("processos_vencimento_proximo", \App\Model\Processo::listarVencimentoProximos());
    $smarty->assign("documentos_vencimento_proximo", \App\Model\Documento::listarVencimentoProximos());
}
