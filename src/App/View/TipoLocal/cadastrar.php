<?php

if (!isset($smarty)) {
    $modal = true;
    include_once '../../../../bootstrap.php';
    include_once '../../../../_config/smarty.config.php';
    $smarty->template_dir = APP_PATH . '/src/App/View/TipoLocal/Templates/';
    $smarty->assign('app_url', APP_URL);
} else {
    $modal = false;
}
$smarty->assign('page_title', 'Nova Tipo de Localização Física');
$smarty->assign('page_icon', 'fa fa-plus');
$smarty->assign('page_description', 'cadastre um novo tipo de local de arquivamento físico');
$smarty->assign("acao", "inserir");
$smarty->assign("local", new \App\Model\TipoLocal());
$smarty->assign('modal', $modal);
if ($modal) {
    $smarty->display('formulario.tpl');
}


