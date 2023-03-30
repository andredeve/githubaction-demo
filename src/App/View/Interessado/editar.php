<?php

$smarty->assign('page_title', 'Editar Interessado');
$smarty->assign('page_icon', 'fa fa-edit');
$smarty->assign("acao", "atualizar");
$smarty->assign("interessado", $_REQUEST['objeto']);
include VIEW_PATH . 'Interessado/_assign.php';
$smarty->assign('modal', false);
