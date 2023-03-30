<?php
include_once APP_PATH . 'bootstrap.php';
include_once APP_PATH . '_config/smarty.config.php';

$smarty->template_dir = APP_PATH . '/src/App/View/Anexo/Templates/';
$smarty->assign("historico", $_REQUEST["historico"]);