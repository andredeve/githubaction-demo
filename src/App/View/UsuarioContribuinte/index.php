<?php
use App\Controller\IndexController;

$smarty->assign("page_title", 'Listagem de ' . IndexController::getParametosConfig()["contribuinte"]);
$smarty->assign('page_icon', 'fa fa-users');
$smarty->assign('usuarios', $_REQUEST['registros']);