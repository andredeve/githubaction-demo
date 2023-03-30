<?php

use App\Model\Setor;


if (!isset($smarty)) {
    $modal = true;
    include_once '../../../../bootstrap.php';
    include_once '../../../../_config/smarty.config.php';
    $smarty->template_dir = APP_PATH . '/src/App/View/Setor/Templates/';
    $smarty->assign('app_url', APP_URL);
} else {
    $modal = false;
}

$smarty->assign('page_title', 'Novo Setor');
$smarty->assign('page_icon', 'fa fa-plus');
$smarty->assign('page_description', 'cadastre um novo setor');
$smarty->assign("acao", "inserir");
$setor = new Setor();
$setor->setIsAtivo(true);
$setor->setDisponivelTramite(true);
$smarty->assign("setor", $setor);
$smarty->assign('setor_selecionado', null);
include VIEW_PATH . 'Setor/_assign.php';
$smarty->assign('modal', $modal);
if ($modal) {
    $smarty->display('formulario.tpl');
}
