<?php

use App\Model\Usuario;

include '../../../../bootstrap.php';
require_once APP_PATH . '_config/smarty.config.php';
$smarty->template_dir = VIEW_PATH . "Usuario/Templates/";
$smarty->assign('app_url', APP_URL);


$usuario = (new Usuario())->buscar(filter_input(INPUT_POST, 'entidade_id', FILTER_SANITIZE_NUMBER_INT));


$smarty->assign('usuario', $usuario);
$smarty->display('visualizar.tpl');
