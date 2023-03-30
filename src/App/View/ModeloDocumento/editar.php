<?php
$smarty->assign('page_title', 'Editar Modelo de Documento');
$smarty->assign('page_icon', 'fa fa-edit');
$smarty->assign("acao", "atualizar");
$smarty->assign("modelo", $_REQUEST['objeto']);
