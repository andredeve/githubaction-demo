<?php
$smarty->assign("page_title", 'Listagem de Fluxogramas');
$smarty->assign('page_icon', 'fa fa-exchange');
$smarty->assign('fluxogramas', $_REQUEST['registros']);
