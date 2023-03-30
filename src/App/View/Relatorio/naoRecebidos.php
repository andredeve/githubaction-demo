<?php

use App\Model\Processo;

$nomenclatura = \Core\Controller\AppController::getParametosConfig()['nomenclatura'];
$smarty->assign('page_title', $nomenclatura . 's não Recebidos');
$smarty->assign('page_icon', 'fa fa-files-o');
$smarty->assign('groupColumn', "");
$smarty->assign('processos', (new Processo())->listarReceber());
include VIEW_PATH . 'Relatorio/_assign.php';
