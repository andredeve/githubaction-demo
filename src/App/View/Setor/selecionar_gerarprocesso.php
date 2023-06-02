<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 23/01/2019
 * Time: 08:33
 */

use App\Controller\UsuarioController;
use Core\Controller\AppController;


include '../../../../bootstrap.php';
require_once APP_PATH . '_config/smarty.config.php';
$smarty->template_dir = VIEW_PATH . "Setor/Templates/";
$smarty->assign('app_url', APP_URL);
$usuario_logado = UsuarioController::getUsuarioLogadoDoctrine();
$smarty->assign('setores', $usuario_logado->getSetores());
$nomenclatura = AppController::getParametosConfig('nomenclatura');
$smarty->assign('nomenclatura', $nomenclatura);
$smarty->assign('processo', $_POST['id']);
$smarty->display('selecionar_gerarprocesso.tpl');
