<?php

use App\Model\Assunto;

if (!isset($smarty)) {
    $modal = true;
    include_once '../../../../bootstrap.php';
    include_once '../../../../_config/smarty.config.php';
    $smarty->template_dir = APP_PATH . '/src/App/View/Assunto/Templates/';
    $smarty->assign('app_url', APP_URL);
} else {
    $modal = false;
}
$smarty->assign('page_title', 'Novo Assunto');
$smarty->assign('page_icon', 'fa fa-plus');
$smarty->assign('page_description', 'cadastre um novo assunto');
$smarty->assign("acao", "inserir");
$assunto = new Assunto();
$assunto->setIsAtivo(true);
$smarty->assign("assunto", $assunto);
include VIEW_PATH . 'Assunto/_assign.php';
$smarty->assign('modal', $modal);
if ($modal) {
    $smarty->display('formulario.tpl');
}
