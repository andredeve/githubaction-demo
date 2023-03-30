<?php

use App\Controller\IndexController;
use App\Model\Campo;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Campo/Templates/';
$smarty->assign('app_url', APP_URL);
$smarty->assign('page_description', 'editar campo');
$smarty->assign("acao", "atualizar");
$smarty->assign("campo", (new Campo())->buscar($_POST['entidade_id']));
$smarty->assign("nomenclatura", IndexController::getParametosConfig()['nomenclatura']);
include VIEW_PATH . 'Campo/_assign.php';
$smarty->display('formulario.tpl');