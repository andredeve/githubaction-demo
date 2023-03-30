<?php

use App\Model\TipoAnexo;
use Core\Controller\AppController;

if (!isset($smarty)) {
    $modal = true;
    include_once '../../../../bootstrap.php';
    include_once '../../../../_config/smarty.config.php';
    $smarty->template_dir = APP_PATH . '/src/App/View/TipoAnexo/Templates/';
    $smarty->assign('app_url', APP_URL);
} else {
    $modal = false;
}
$smarty->assign('page_title', 'Novo Tipo de Anexo');
$smarty->assign('page_icon', 'fa fa-plus');
$smarty->assign('page_description', 'cadastre um novo tipo de anexo de processo');
$smarty->assign("acao", "inserir");
$smarty->assign("tipoAnexo", new TipoAnexo());
$smarty->assign('modal', $modal);
$smarty->assign('parametros', AppController::getParametosConfig());
if ($modal) {
    $smarty->display('formulario.tpl');
}


