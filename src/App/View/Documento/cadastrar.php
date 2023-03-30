<?php
include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';

$smarty->setTemplateDir(APP_PATH . '/src/App/View/Documento/Templates/');
$smarty->assign('app_url', APP_URL);
$smarty->assign('page_description', 'adicione um novo documento');
$smarty->assign("acao", "inserir");
$processo = !empty($_POST['objeto_ref_id']) ? (new \App\Model\Processo())->buscar($_POST['objeto_ref_id']) : unserialize($_SESSION['processo']);
$documento = new \App\Model\Documento();
$documento->setProcesso($processo);
$smarty->assign("processo", $processo);
$smarty->assign("indice", null);
$smarty->assign("documento", $documento);
include VIEW_PATH . 'Documento/_assign.php';
$smarty->display('formulario.tpl');
