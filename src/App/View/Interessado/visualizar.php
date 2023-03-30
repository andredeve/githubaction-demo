<?php


include '../../../../bootstrap.php';
require_once APP_PATH . '_config/smarty.config.php';
$smarty->template_dir = VIEW_PATH . "Interessado/Templates/";
$smarty->assign('app_url', APP_URL);
$interessado = (new \App\Model\Interessado())->buscar(filter_input(INPUT_POST, 'interessado_id', FILTER_SANITIZE_NUMBER_INT));
$smarty->assign('interessado', $interessado);
$smarty->display('visualizar.tpl');
