<?php

use App\Model\StatusProcesso;

$smarty->assign("page_title", \Core\Controller\AppController::getParametosConfig('nomenclatura') . 's enviados por você');
$smarty->assign('page_icon', 'fa fa-th-list');
$smarty->assign('status_processo', (new StatusProcesso())->listar());
$smarty->assign('nomenclatura', ucfirst(App\Controller\IndexController::getParametosConfig()['nomenclatura']));
$smarty->assign('file_version', uniqid());
$smarty->assign('selected', 'enviados');