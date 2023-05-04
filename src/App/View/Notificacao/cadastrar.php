<?php

use App\Model\Notificacao;
use App\Model\Processo;
use App\Model\Anexo;
use App\Controller\UsuarioController;

if (!isset($smarty)) {
    $modal = true;
    include_once '../../../../bootstrap.php';
    include_once '../../../../_config/smarty.config.php';
    $smarty->template_dir = APP_PATH . '/src/App/View/Notificacao/Templates/';
    $smarty->assign('app_url', APP_URL);
} else {
    $modal = false;
}

if (isset($_POST["processo_id"])){
    
    $processo = (new Processo())->buscar($_POST["processo_id"]);
    $anexo = (new Anexo())->buscar($_POST["anexo_id"]);
    $smarty->assign('processo', $processo);
    $smarty->assign('anexo', $anexo);
    $conteudo = array('Processo: ' . $processo->getNumero(), ' Tipo de Documento: ' . $anexo->getTipo(), ' Descrição: ' . $anexo->getDescricao(), ' Data: ' . $anexo->getData(true), ' Número: ' . $anexo->getNumero());
    $smarty->assign('conteudo', $conteudo);

}
$smarty->assign('page_title', 'Nova Notificação');
$smarty->assign('page_icon', 'fa fa-plus');
$smarty->assign('page_description', 'crie uma nova notificação');
$smarty->assign("acao", "inserir");
$smarty->assign('usuario_logado', UsuarioController::getUsuarioLogadoDoctrine());
$notificacao = new Notificacao();
$smarty->assign("notificacao", $notificacao);
include VIEW_PATH . 'Notificacao/_assign.php';
$smarty->assign('modal', $modal);
if ($modal) {
    $smarty->display('formulario.tpl');
}
