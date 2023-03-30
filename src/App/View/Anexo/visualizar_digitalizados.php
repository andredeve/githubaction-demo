<?php

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Anexo/Templates/';
$smarty->assign('app_url', APP_URL);
//$processo = !empty($_GET['processo_id']) ? (new Processo())->buscar($_GET['processo_id']) : unserialize($_SESSION['processo']);
$anexo = isset($_GET['anexo_id']) && !empty($_GET['anexo_id']) ? (new \App\Model\Anexo())->buscar($_GET['anexo_id']) : unserialize($_SESSION['processo'])->getAnexos()->get($_GET['indice']);
$smarty->assign('anexo', $anexo);
$images = json_decode($_GET['imagens']);
$smarty->assign("imagens", $images);
$smarty->display('visualizar_digitalizados.tpl');
