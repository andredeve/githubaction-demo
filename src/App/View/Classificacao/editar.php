<?php

$smarty->assign('page_title', 'Editar Classificação de Documento');
$smarty->assign('page_icon', 'fa fa-edit');
$smarty->assign("acao", "atualizar");
$smarty->assign("classificacao", $_REQUEST['objeto']);
include VIEW_PATH . 'Classificacao/_assign.php';
$smarty->assign('modal', false);
