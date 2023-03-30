<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 14/12/2018
 * Time: 07:53
 */

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Tramite/Templates/';
$smarty->assign('tramite', $tramite);
$smarty->assign('app_url', APP_URL);
$smarty->display('cancelar.tpl');
