<?php
$smarty->assign("page_title", 'Listagem de Usuários');
$smarty->assign('page_icon', 'fa fa-users');
$smarty->assign('usuarios', $_REQUEST['registros']);

