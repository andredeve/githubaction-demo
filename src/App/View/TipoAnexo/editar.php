<?php

use Core\Controller\AppController;

$smarty->assign('page_title', 'Editar Tipo de Anexo');
$smarty->assign('page_icon', 'fa fa-edit');
$smarty->assign("acao", "atualizar");
$smarty->assign("tipoAnexo", $_REQUEST['objeto']);
$smarty->assign('modal', false);
$smarty->assign('parametros', AppController::getParametosConfig());
