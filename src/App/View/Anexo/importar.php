<?php

use App\Model\Anexo;
use App\Model\Processo;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Anexo/Templates/';
$smarty->assign('app_url', APP_URL);
$config = App\Controller\IndexController::getConfig();
$smarty->assign('config', $config );
$smarty->assign('processo', (new Processo())->buscar($_POST['processo_id']) );


//$url = $config["lxfiorilli"]."index.php?entidade=index&method=listarAnoBanco" ;
$retorno = file_get_contents($config["lxfiorilli_interno"]."index.php?entidade=index&method=listarAnoBanco" );
$retorno = json_decode($retorno);
$smarty->assign('bancos', $retorno->bancos );


$smarty->display('importar.tpl');
