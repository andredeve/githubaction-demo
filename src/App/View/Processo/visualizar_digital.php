<?php

use App\Controller\IndexController;
use App\Model\Processo;

/**
 * @var Smarty $smarty
 */

if(empty($_POST['carregadoByController'])){
    include_once '../../../../bootstrap.php';
    include_once '../../../../_config/smarty.config.php';
    include_once APP_PATH . 'lib/pdf-merger/tcpdf/TCPDI.php';
    $smarty->setTemplateDir(APP_PATH . '/src/App/View/Processo/Templates/');
    $processo_id = filter_input(INPUT_GET, "processo_id", FILTER_SANITIZE_NUMBER_INT);
}else{
    include_once APP_PATH . 'lib/pdf-merger/tcpdf/TCPDI.php';
    $processo_id = $_POST['processo_id'];
}

$_POST["qtdPaginas"] = 0;
$smarty->assign('app_url', APP_URL);
$processo = (new Processo())->buscar($processo_id);
$processo->gerarCapa();
$smarty->assign("processo", $processo);
$smarty->assign("file_version", uniqid());
$smarty->assign('parametros', IndexController::getParametosConfig());
if(empty($_POST['carregadoByController'])){
    $smarty->display('visualizar_digital.tpl');
}


