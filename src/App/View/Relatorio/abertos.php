<?php

use App\Model\Processo;

$nomenclatura = \Core\Controller\AppController::getParametosConfig()['nomenclatura'];
$smarty->assign('page_title', $nomenclatura . 's em aberto');
$smarty->assign('page_icon', 'fa fa-files-o');
$smarty->assign('groupColumn', "");
$smarty->assign('processos', (new Processo())->listarEmAberto());
include VIEW_PATH . 'Relatorio/_assign.php';
