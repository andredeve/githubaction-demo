<?php
use App\Controller\IndexController;

$smarty->assign('page_title', 'Editar ' . IndexController::getParametosConfig()["contribuinte"]);
$smarty->assign('page_icon', 'fa fa-edit');
$smarty->assign("acao", "atualizar");
$usuario = $_REQUEST['objeto'];
$smarty->assign("usuario", $usuario);