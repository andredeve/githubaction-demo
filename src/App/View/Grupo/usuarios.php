<?php

use App\Model\Grupo;

include '../../../../bootstrap.php';
require_once APP_PATH . '_config/smarty.config.php';
$smarty->template_dir = VIEW_PATH . "Grupo/Templates/";
$smarty->assign('app_url', APP_URL);
$grupo = (new Grupo())->buscar(filter_input(INPUT_POST, 'grupo_id', FILTER_SANITIZE_NUMBER_INT));
$smarty->assign('grupo', $grupo);
$smarty->display('usuarios.tpl');

