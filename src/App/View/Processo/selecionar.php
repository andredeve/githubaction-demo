<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 23/01/2019
 * Time: 08:33
 */
include '../../../../bootstrap.php';
require_once APP_PATH . '_config/smarty.config.php';
$smarty->template_dir = VIEW_PATH . "Processo/Templates/";
$smarty->assign('app_url', APP_URL);
$smarty->display('selecionar.tpl');
