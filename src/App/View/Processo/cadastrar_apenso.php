<?php

use App\Controller\UsuarioController;
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 18/01/2019
 * Time: 09:12
 */
include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';

$user = UsuarioController::getUsuarioLogadoDoctrine();
$smarty->template_dir = APP_PATH . '/src/App/View/Processo/Templates/';
$processo = !empty($_POST['processo_id']) ? (new \App\Model\Processo())->buscar($_POST['processo_id']) : unserialize($_SESSION['processo']);
$smarty->assign('processo_pai', $processo);
$smarty->assign('processo', clone $processo);
$smarty->assign("usuario", $user);
$smarty->assign('acao', 'apenso');
$smarty->assign("usuario", $user);
$smarty->assign('app_url', APP_URL);
include VIEW_PATH . 'Processo/_assign.php';
$smarty->display('cadastrar_apenso.tpl');