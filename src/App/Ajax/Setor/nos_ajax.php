<?php

use App\Model\Setor;
use App\Model\Usuario;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Setor/Templates/';
$smarty->assign('app_url', APP_URL);

$usuario = new Usuario();
$setor = new Setor();
$setores_permitidos = array();
if (isset($_GET['usuario_id']) && !empty($_GET['usuario_id'])) {
    $usuario = $usuario->buscar($_GET['usuario_id']);
    $setores_permitidos = $usuario->getSetoresIds();
} else if (isset($_GET['setor_id']) && !empty($_GET['setor_id'])) {
    $setores_permitidos = array($_GET['setor_id']);
}
$smarty->assign("setores_permitidos", $setores_permitidos);
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $setor = $setor->buscar($_GET['id']);
    $smarty->assign("setores", $setor->getSetoresFilhos());
} else {
    $smarty->assign("setores", $setor->listarSetoresPai());
}
$smarty->display('no_arvore.tpl');
