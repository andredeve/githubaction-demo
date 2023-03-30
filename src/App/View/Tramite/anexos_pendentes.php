<?php
include '../../../../bootstrap.php';
require_once APP_PATH . '_config/smarty.config.php';
$smarty->template_dir = VIEW_PATH . "Tramite/Templates/";
$smarty->assign('app_url', APP_URL);
$smarty->display('anexos_pendentes.tpl');