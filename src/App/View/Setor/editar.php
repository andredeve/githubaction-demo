<?php

$smarty->assign('page_title', 'Editar Setor');
$smarty->assign('page_icon', 'fa fa-edit');
$smarty->assign("acao", "atualizar");
$setor = $_REQUEST['objeto'];
$smarty->assign("setor", $setor);
$smarty->assign('setor_selecionado', array($setor->getSetorPai()->getId()));
include VIEW_PATH . 'Setor/_assign.php';