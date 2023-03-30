<?php

$smarty->assign('page_title', 'Editar Tipo de Local Físico de Arquivamento');
$smarty->assign('page_icon', 'fa fa-edit');
$smarty->assign("acao", "atualizar");
$smarty->assign("local", $_REQUEST['objeto']);
$smarty->assign('modal', false);
