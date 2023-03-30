<?php

$smarty->assign('page_title', 'Editar Fluxograma');
$smarty->assign('page_icon', 'fa fa-edit');
$smarty->assign("acao", "atualizar");
$smarty->assign('fluxograma', $_REQUEST['objeto']);
include VIEW_PATH . 'Fluxograma/_assign.php';

