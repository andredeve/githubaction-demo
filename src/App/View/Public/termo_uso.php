<?php

use Core\Controller\AppController;

        
$app_config = AppController::getConfig();

$smarty->assign('page_title', 'Termos de uso');
$smarty->assign('app_url', APP_URL);
$smarty->assign('app_config', $app_config);
$smarty->assign('termos', AppController::getTermosUso());
$smarty->assign('contribuinte', AppController::getParametosConfig('contribuinte'));
$smarty->assign('cliente', AppController::getClienteConfig());
