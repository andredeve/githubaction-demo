<?php

use App\Model\StatusProcesso;
$smarty->assign("page_title", \Core\Controller\AppController::getParametosConfig('nomenclatura') . 's em Aberto');
$smarty->assign('page_icon', 'fa fa-mail-forward');
$smarty->assign('status_processo', (new StatusProcesso())->listar());
$smarty->assign('nomenclatura', ucfirst(App\Controller\IndexController::getParametosConfig()['nomenclatura']));
$smarty->assign('selected', 'abertos');

