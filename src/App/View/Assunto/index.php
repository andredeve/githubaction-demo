<?php

$smarty->assign("page_title", 'Listagem de Assuntos cadastrados');
$smarty->assign('page_icon', 'fa fa-th-list');
$smarty->assign('assuntos', $_REQUEST['registros']);

