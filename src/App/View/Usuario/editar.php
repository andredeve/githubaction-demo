<?php

$smarty->assign('page_title', 'Editar Usuário');
$smarty->assign('page_icon', 'fa fa-edit');
$smarty->assign("acao", "atualizar");
$usuario = $_REQUEST['objeto'];
$smarty->assign("usuario", $usuario);
$smarty->assign('setor_selecionado', $usuario->getSetoresIds());
include VIEW_PATH . 'Usuario/_assign.php';
