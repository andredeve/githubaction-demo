<?php

include '../../../../bootstrap.php';
require_once APP_PATH . '_config/smarty.config.php';
$smarty->template_dir = VIEW_PATH . "Setor/Templates/";
$smarty->assign('app_url', APP_URL);
$setor = (new \App\Model\Setor())->buscar(filter_input(INPUT_POST, 'setor_id', FILTER_SANITIZE_NUMBER_INT));
$smarty->assign('setor', $setor);
$smarty->display('usuarios.tpl');

