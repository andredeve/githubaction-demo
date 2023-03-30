<?php
use App\Controller\UsuarioController;
use App\Enum\TipoUsuario;

include '../../../../bootstrap.php';
require_once APP_PATH . '_config/smarty.config.php';
$smarty->template_dir = VIEW_PATH . "Assunto/Templates/";
$smarty->assign('usuario_logado', UsuarioController::getUsuarioLogadoDoctrine());
$smarty->assign('app_url', APP_URL);
$smarty->display('selecionar.tpl');
