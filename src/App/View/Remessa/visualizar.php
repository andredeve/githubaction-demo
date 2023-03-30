<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 08/01/2019
 * Time: 14:57
 */
include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Remessa/Templates/';
$smarty->assign('app_url', APP_URL);
$smarty->assign("remessa", (new \App\Model\Remessa())->buscar($_POST['remessa_id']));
$smarty->display('visualizar.tpl');