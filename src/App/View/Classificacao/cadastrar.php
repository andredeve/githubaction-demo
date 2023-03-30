<?php

use App\Model\Classificacao;

if (!isset($smarty)) {
    $modal = true;
    include_once '../../../../bootstrap.php';
    include_once '../../../../_config/smarty.config.php';
    $smarty->template_dir = APP_PATH . '/src/App/View/Classificacao/Templates/';
    $smarty->assign('app_url', APP_URL);
} else {
    $modal = false;
}
$smarty->assign('page_title', 'Nova Classificação');
$smarty->assign('page_icon', 'fa fa-plus');
$smarty->assign('page_description', 'cadastre uma nova classificação de documento');
$smarty->assign("acao", "inserir");
$smarty->assign("classificacao", new Classificacao());
include VIEW_PATH . 'Classificacao/_assign.php';
$smarty->assign('modal', $modal);
if ($modal) {
    $smarty->display('formulario.tpl');
}