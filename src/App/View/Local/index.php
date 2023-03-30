<?php

$smarty->assign("page_title", 'Listagem de Locais Físicos de Arquivamento');
$smarty->assign('page_icon', 'fa fa-th-list');
$smarty->assign('locais', $_REQUEST['registros']);

