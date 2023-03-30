<?php

$smarty->assign('page_title', 'Editar Categoria de Documento');
$smarty->assign('page_icon', 'fa fa-edit');
$smarty->assign("acao", "atualizar");
$smarty->assign("categoria", $_REQUEST['objeto']);
include VIEW_PATH . 'CategoriaDocumento/_assign.php';
$smarty->assign('modal', false);
