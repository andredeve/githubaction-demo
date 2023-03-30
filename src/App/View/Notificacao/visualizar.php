<?php

if (!isset($smarty)) {
    $modal = true;
    include_once '../../../../bootstrap.php';
    include_once '../../../../_config/smarty.config.php';
    $smarty->template_dir = APP_PATH . '/src/App/View/Notificacao/Templates/';
    $smarty->assign('app_url', APP_URL);
} else {
    $modal = false;
}
$notificacao = $_REQUEST['notificacao'];
$smarty->assign('page_title', 'Visualizar Notificação #' . $notificacao->getNumero());
$smarty->assign('page_icon', 'fa fa-search');
$smarty->assign("notificacao", $notificacao);
$smarty->assign('modal', $modal);
include VIEW_PATH . 'Notificacao/_assign.php';
if ($modal) {
    $smarty->display('visualizar.tpl');
}