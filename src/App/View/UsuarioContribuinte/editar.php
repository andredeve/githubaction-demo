<?php

$smarty->assign('page_title', 'Editar Contribuinte');
$smarty->assign('page_icon', 'fa fa-edit');
$smarty->assign("acao", "atualizar");
$usuario = $_REQUEST['objeto'];
$smarty->assign("usuario", $usuario);