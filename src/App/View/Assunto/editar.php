<?php

$smarty->assign('page_title', 'Editar Assunto');
$smarty->assign('page_icon', 'fa fa-edit');
$smarty->assign("acao", "atualizar");
$assunto = $_REQUEST['objeto'];
$smarty->assign("assunto", $assunto);
$smarty->assign('setor_selecionado', $assunto->getSetoresIds());
include VIEW_PATH . 'Assunto/_assign.php';
$smarty->assign('modal', false);