<?php

use App\Model\Processo;

$modal = false;
if (!isset($smarty)) {
    include_once '../../../../bootstrap.php';
    include_once '../../../../_config/smarty.config.php';
    $smarty->template_dir = APP_PATH . '/src/App/View/Processo/Templates/';
    $smarty->assign('app_url', APP_URL);
    $modal = true;
}
$smarty->assign('page_title', 'Acesso ' . \Core\Controller\AppController::getParametosConfig('nomenclatura') . ' Sigiloso');
$smarty->assign('page_icon', 'fa fa-lock');
$smarty->assign('modal', $modal);
if ($modal) {
    $smarty->assign('processo', (new Processo())->buscar($_POST['processo_id']));
    $smarty->display("autenticar.tpl");
} else {
    $smarty->assign('processo', $_REQUEST['objeto']);
}
