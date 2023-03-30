<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 18/01/2019
 * Time: 10:01
 */
include '../../../../bootstrap.php';
require_once APP_PATH . '_config/smarty.config.php';
$smarty->template_dir = VIEW_PATH . "Interessado/Templates/";
$smarty->assign('app_url', APP_URL);
$smarty->display('pesquisar.tpl');
