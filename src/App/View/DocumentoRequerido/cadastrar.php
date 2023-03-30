<?php
include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';

use App\Model\DocumentoRequerido;

$modal = true;
$config = Core\Controller\AppController::getConfig();
$smarty->template_dir = APP_PATH . '/src/App/View/DocumentoRequerido/Templates/';
$smarty->assign('app_url', APP_URL);
$smarty->assign('page_description', 'Adicionar requisição de documento(s)');
$documentoRequerido = new DocumentoRequerido();
if(isset($_POST['documento_requerido_id']) && !empty($_POST['documento_requerido_id'])){
    $smarty->assign("acao", "atualizar");
    $documentoRequerido = $documentoRequerido->buscar($_POST['documento_requerido_id']);
}else{
    $smarty->assign("acao", "inserir");
    $tramiteCadastro = (new App\Model\Tramite())->buscar($_POST['tramite_id']);
    $documentoRequerido->setTramiteCadastro($tramiteCadastro);
}
$smarty->assign("documentoRequerido", $documentoRequerido);
include VIEW_PATH . 'DocumentoRequerido/_assign.php';
$smarty->display('formulario.tpl');