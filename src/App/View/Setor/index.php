<?php

$smarty->assign("page_title", 'Listagem de Setores cadastrados');
$smarty->assign('page_icon', 'fa fa-th-list');
$smarty->assign('setores', $_REQUEST['registros']);

