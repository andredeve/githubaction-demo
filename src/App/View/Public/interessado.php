<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 14/01/2019
 * Time: 08:21
 */
$app_config = \Core\Controller\AppController::getConfig();
$smarty->assign('cliente_config', \Core\Controller\AppController::getClienteConfig());
$smarty->assign('app_url', APP_URL);
$smarty->assign('app', $app_config);
$smarty->assign('processo', $_REQUEST['processo']);
$smarty->assign("nomenclatura",\Core\Controller\AppController::getParametosConfig()['nomenclatura']);
$smarty->assign('anos', (new \App\Model\Processo())->getExercicios());