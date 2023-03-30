<?php

use App\Model\Processo;
use App\Controller\UsuarioController;
use App\Enum\TipoUsuario;

$smarty->assign("page_title", 'Cadastro de ' . \Core\Controller\AppController::getParametosConfig('nomenclatura') . ' Finalizado');
$smarty->assign('page_icon', 'fa fa-check');
$smarty->assign('processo', (new Processo())->buscar($_REQUEST['processo_id']));
$smarty->assign('usuarioEhInteressado', UsuarioController::isInteressado());
