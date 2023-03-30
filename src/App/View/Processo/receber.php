<?php

use App\Model\StatusProcesso;

$smarty->assign('file_version', uniqid());
$smarty->assign("pode_desarquivar", false);
$smarty->assign("page_title", 'Meus ' . \Core\Controller\AppController::getParametosConfig('nomenclatura') . 's a Receber');
$smarty->assign('page_icon', 'fa fa-th-list');
$smarty->assign('status_processo', (new StatusProcesso())->listarAtivos());
$smarty->assign('nomenclatura', ucfirst(App\Controller\IndexController::getParametosConfig()['nomenclatura']));
$smarty->assign('filtro_remessa', true);
$smarty->assign('selected', 'receber');