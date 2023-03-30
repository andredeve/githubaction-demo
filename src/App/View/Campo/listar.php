<?php

use App\Model\SetorFase;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Campo/Templates/';
$smarty->assign('app_url', APP_URL);
$setorFase = (new SetorFase())->buscar($_POST['objeto_ref_id']);
$smarty->assign("setor_fase", $setorFase);
$smarty->assign("campos", $setorFase->getCampos());
$smarty->display('tabela.tpl');
$smarty->assign("nomenclatura", \App\Controller\IndexController::getParametosConfig()['nomenclatura']);