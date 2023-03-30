<?php

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Documento/Templates/';
$smarty->assign('app_url', APP_URL);
$processo = !empty($_POST['objeto_ref_id']) ? (new \App\Model\Processo())->buscar($_POST['objeto_ref_id']) : unserialize($_SESSION['processo']);
$smarty->assign("documentos", $processo->getDocumentos());
$smarty->assign("processo", $processo);
$smarty->display('listar.tpl');
