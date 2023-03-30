<?php

$smarty->assign('page_title', 'Editar Localização Física de Processo');
$smarty->assign('page_icon', 'fa fa-edit');
$smarty->assign("acao", "atualizar");
$smarty->assign("localizacao", $_REQUEST['objeto']);
include VIEW_PATH . 'LocalizacaoFisica/_assign.php';
