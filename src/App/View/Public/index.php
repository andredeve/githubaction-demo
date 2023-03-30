<?php

use App\Controller\IndexController;
use App\Controller\UsuarioController;
use App\Model\Notificacao;
use App\Model\Processo;
use App\Model\Solicitacao;
use Core\Controller\AppController;
use Core\Util\Functions;
use App\Controller\AssinaturaController;
use App\Enum\TipoUsuario;

$smarty->assign('app_url', APP_URL);
$smarty->assign('app', AppController::getConfig());
$contribuinteHabilitado = AppController::contribuinteHabilitado();
$smarty->assign('contribuinteHabilitado', $contribuinteHabilitado);
$smarty->assign('parametros', AppController::getParametosConfig());
$smarty->assign('cliente_config', AppController::getClienteConfig());
$smarty->assign('data_atual', Functions::dataAtual());
$smarty->assign('nomenclatura',strtoupper(AppController::getParametosConfig()['nomenclatura']));
$usuario_logado = UsuarioController::getUsuarioLogadoDoctrine();

if(isset($_REQUEST['toker_user'])){
    $smarty->assign('token_user',$_REQUEST['toker_user']);
    $smarty->assign('usuario_id',$usuario_logado->getId());
}
else {
    $smarty->assign('token_user','');
    $smarty->assign('usuario_id','');
}
$smarty->assign('usuario_logado', $usuario_logado);
if (isset($_REQUEST['breadcrumb'])) {
    $smarty->assign('breadcrumb', $_REQUEST['breadcrumb']);
}
$smarty->assign('parametros', IndexController::getParametosConfig());
/* * *********************************************************************************************************** */
/* ================================        Processo    =============================================== */
/* * ********************************************************************************************************** */
$usuarioEhInteressado = ($usuario_logado !== null && $usuario_logado->isInteressado());
//$smarty->assign('usuarioEhInteressado', ($usuarioEhInteressado) ? 1:0);

$processo = new Processo();
$smarty->assign('anos', $processo->getExercicios());
$qtde_receber = Processo::getQtdeListagem('receber');
$qtde_vencidos = Processo::getQtdeListagem('vencidos');
$qtde_processos_vencidos = Processo::getQtdeListagem("processo-vencido");
$smarty->assign("qtde_receber", $qtde_receber);
$smarty->assign("qtde_vencidos", $qtde_vencidos);
$smarty->assign("qtde_processos_vencidos", $qtde_processos_vencidos);
$smarty->assign("qtde_enviados", Processo::getQtdeListagem('enviados'));
$smarty->assign("qtde_aberto", Processo::getQtdeListagem('abertos'));
$smarty->assign("qtde_arquivados", Processo::getQtdeListagem('arquivados'));
if ($contribuinteHabilitado){
    $smarty->assign("qtde_contribuintes", Processo::getQtdeListagem('contribuintes'));
}
$smarty->assign("qtde_solicitacoes", Solicitacao::getQtdeListagem());

if(isset($usuario_logado) && !empty($usuario_logado) && $usuario_logado->getToken() && $usuario_logado->getTipo() != TipoUsuario::INTERESSADO){
    $assiRequ = new AssinaturaController();
    $objResquestQtdAssinatura = $assiRequ->qtdRequisicaoAssinatura($usuario_logado->getId(), $usuario_logado->getToken());
    if(is_object($objResquestQtdAssinatura)){
        if($objResquestQtdAssinatura->signatario){
            $smarty->assign("texto_qtd_requisicao_assinatura", "Requisições de Assinatura");
        } else{
            $smarty->assign("texto_qtd_requisicao_assinatura", "Em Processo");
        }
        $smarty->assign("qtd_requisicao_assinatura", $objResquestQtdAssinatura->total_requisicao_assinatura);
        $smarty->assign("usuario_signatario", $objResquestQtdAssinatura->signatario);
    } else {
        $smarty->assign("usuario_signatario", false);
    }
} else{
    $smarty->assign("usuario_signatario", false);
}
/* * *********************************************************************************************************** */
/* ================================        Notificações    =============================================== */
/* * ********************************************************************************************************** */
$notificacao = new Notificacao();
$notificacoes_enviadas = $notificacao->listarEnviadas();
$notificacoes_recebidas = $notificacao->listarRecebidas();
$notificacoes_arquivadas = $notificacao->listarArquivadas();
$smarty->assign("notificacoes_enviadas", $notificacoes_enviadas);
$smarty->assign("notificacoes_recebidas", $notificacoes_recebidas);
$smarty->assign("notificacoes_arquivadas", $notificacoes_arquivadas);
$smarty->assign("qtde_notificacoes_enviadas", count($notificacoes_enviadas));
$smarty->assign("qtde_notificacoes_recebidas", count($notificacoes_recebidas));
$smarty->assign("qtde_notificacoes_arquivadas", count($notificacoes_arquivadas));
$smarty->assign("exercicio_atual", \App\Controller\ProcessoController::getExercicioAtual());
/* * *********************************************************************************************************** */
/* ================================        Alertas    =============================================== */
/* * ********************************************************************************************************** */
$alertas = array();
if ($qtde_receber > 0) {
    $alertas[] = array('classe' => '', 'href' => APP_URL . 'processo/receber', 'mensagem' => "Existe $qtde_receber processo(s) para ser(em) recebido(s).");
}
if ($qtde_vencidos > 0) {
    $alertas[] = array('classe' => 'btn-ver-vencidos', 'href' => 'javascript:', 'mensagem' => "Existe $qtde_vencidos processo(s) com atraso para tramitar.");
}
$smarty->assign('alertas', $alertas);
$smarty->assign('file_version', AppController::getConfig()['file_version']);