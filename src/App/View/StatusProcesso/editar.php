<?php

$smarty->assign('page_title', 'Editar Status de Processo');
$smarty->assign('page_icon', 'fa fa-edit');
$smarty->assign("acao", "atualizar");
$smarty->assign("status", $_REQUEST['objeto']);
