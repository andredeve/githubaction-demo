<?php

use Core\Controller\AppController;
use App\Controller\InteressadoController;


$app_config = AppController::getConfig();
$smarty->assign('page_title', AppController::getParametosConfig()['nomenclatura'].' Externo');
$smarty->assign('app', AppController::getConfig());
$smarty->assign('interessado_logado', InteressadoController::getInteressadoLogado());
$smarty->assign('app_url', APP_URL);
$smarty->assign('app_path', APP_PATH);
$smarty->assign('app_config', $app_config);
$smarty->assign('cliente_config', AppController::getClienteConfig());
$smarty->assign('parametros', AppController::getParametosConfig());
$smarty->assign('nomenclatura',strtoupper(AppController::getParametosConfig()['nomenclatura']));
$smarty->assign("data_site_key", $app_config['data_site_key']);
$smarty->assign('file_version', $app_config['file_version']);