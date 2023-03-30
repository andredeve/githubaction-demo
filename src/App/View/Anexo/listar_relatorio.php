<?php

use App\Model\Anexo;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Anexo/Templates/';
$smarty->assign('app_url', APP_URL);
$smarty->assign('file_version', uniqid());


//if(!isset($_POST['data_periodo_ini']) || empty($_POST['data_periodo_ini'])){
//    $dataIni = new DateTime();
//    $dataIni->sub(new DateInterval('P30D'));
//    $_POST['data_periodo_ini'] = $dataIni->format("d/m/Y");
//    $dataFim = new DateTime();
//    $_POST['data_periodo_fim'] = $dataFim->format("d/m/Y");
//}


$smarty->assign("anexos", (new Anexo())->listarAnexos($_POST['tipo_documento_id'], $_POST['data_periodo_ini'], $_POST['data_periodo_fim'], $_POST['usuario_id']));

$anexos = (new Anexo())->listarQtde("tipo" , $_POST['tipo_documento_id'], $_POST['data_periodo_ini'], $_POST['data_periodo_fim'], $_POST['usuario_id']);

$smarty->assign("anexos_por_tipo", $anexos);
$smarty->display('listar_relatorio.tpl');
