<?php

use Core\Controller\AppController;

$smarty->assign("page_title", 'Listagem de Tipos de Anexo');
$smarty->assign('page_icon', 'fa fa-th-list');
$smarty->assign('tipos_anexo', $_REQUEST['registros']);
$smarty->assign('parametros', AppController::getParametosConfig());

