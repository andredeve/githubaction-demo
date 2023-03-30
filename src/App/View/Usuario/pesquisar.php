<?php
include '../../../../bootstrap.php';
require_once APP_PATH . '_config/smarty.config.php';
$smarty->template_dir = VIEW_PATH . "Usuario/Templates/";
$smarty->assign('app_url', APP_URL);
$smarty->display('pesquisar.tpl');
