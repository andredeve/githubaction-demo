<?php

use App\Model\StatusProcesso;

$smarty->assign('file_version', uniqid());
$smarty->assign("page_title", \Core\Controller\AppController::getParametosConfig('nomenclatura') .'s arquivados');
$smarty->assign('page_icon', 'fa fa-archive');
$smarty->assign('status_processo', (new StatusProcesso())->listar());
$smarty->assign('selected', 'arquivados');

