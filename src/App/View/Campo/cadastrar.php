<?php

use App\Model\Campo;
use App\Model\SetorFase;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Campo/Templates/';
$smarty->assign('app_url', APP_URL);
$smarty->assign('page_description', 'adicione um novo campo');
$smarty->assign("acao", "inserir");
$setorFase = (new SetorFase())->buscar($_POST['objeto_ref_id']);
$campo = new Campo();
$campo->setOrdem(count($setorFase->getCampos()) + 1);
$campo->setIsObrigatorio(true);
$campo->setSetorFase($setorFase);
$smarty->assign("campo", $campo);
include VIEW_PATH . 'Campo/_assign.php';
$smarty->display('formulario.tpl');
$smarty->assign("nomenclatura", \App\Controller\IndexController::getParametosConfig()['nomenclatura']);