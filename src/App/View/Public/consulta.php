<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 11/01/2019
 * Time: 13:46
 */
$app_config = \Core\Controller\AppController::getConfig();
$smarty->assign('cliente_config', \Core\Controller\AppController::getClienteConfig());
$smarty->assign('app_url', APP_URL);
$smarty->assign('app', $app_config);
$smarty->assign('ano_atual', Date('Y'));
$smarty->assign('anos', (new \App\Model\Processo())->getExercicios());
