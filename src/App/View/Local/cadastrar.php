<?php

if (!isset($smarty)) {
    $modal = true;
    include_once '../../../../bootstrap.php';
    include_once '../../../../_config/smarty.config.php';
    $smarty->template_dir = APP_PATH . '/src/App/View/Local/Templates/';
    $smarty->assign('app_url', APP_URL);
} else {
    $modal = false;
}
$smarty->assign('page_title', 'Nova Localização Física');
$smarty->assign('page_icon', 'fa fa-plus');
$smarty->assign('page_description', 'cadastre um novo local de arquivamento físico');
$smarty->assign("acao", "inserir");
$smarty->assign("local", new \App\Model\Local());
$smarty->assign('modal', $modal);
if ($modal) {
    $smarty->display('formulario.tpl');
}


