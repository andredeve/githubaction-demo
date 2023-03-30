<?php

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Documento/Templates/';
$smarty->assign('app_url', APP_URL);
$smarty->assign('page_description', 'editar documento');
$smarty->assign("acao", "atualizar");
$documento = !empty($_POST['entidade_id']) ? (new \App\Model\Documento())->buscar($_POST['entidade_id']) : unserialize($_SESSION['processo'])->getDocumentos()->get($_POST['indice']);
$smarty->assign("indice", $_POST['indice']);
$smarty->assign("documento", $documento);
include VIEW_PATH . 'Documento/_assign.php';
$smarty->display('formulario.tpl');

