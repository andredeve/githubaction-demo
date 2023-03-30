<?php

if (!isset($smarty)) {
    $modal = true;
    include_once '../../../../bootstrap.php';
    include_once '../../../../_config/smarty.config.php';
    $smarty->template_dir = APP_PATH . '/src/App/View/CategoriaDocumento/Templates/';
    $smarty->assign('app_url', APP_URL);
} else {
    $modal = false;
}
$smarty->assign('page_title', 'Nova Categoria de Documento');
$smarty->assign('page_icon', 'fa fa-plus');
$smarty->assign('page_description', 'cadastre uma nova categoria de Documento');
$smarty->assign("acao", "inserir");
$smarty->assign("categoria", new \App\Model\CategoriaDocumento());
include VIEW_PATH . 'CategoriaDocumento/_assign.php';
$smarty->assign('modal', $modal);
if ($modal) {
    $smarty->display('formulario.tpl');
}