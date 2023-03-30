<?php

$smarty->assign("page_title", 'Listagem de Grupos de Usuário');
$smarty->assign('page_icon', 'fa fa-users');
$smarty->assign('grupos', $_REQUEST['registros']);
