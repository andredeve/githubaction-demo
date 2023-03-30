<?php

use App\Controller\UsuarioController;
use App\Model\Setor;
use App\Model\Usuario;

include '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Setor/Templates/';
$smarty->assign('app_url', APP_URL);

$setor = new Setor();
$usuario = new Usuario();
$usuario = UsuarioController::getUsuarioLogado();
$smarty->assign("setores_permitidos", $usuario->getSetoresIds());
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $setor = $setor->buscar($_GET['id']);
    $smarty->assign("setores", $setor->getSetoresFilhos());
} else {
    $smarty->assign("setores", $setor->listarSetoresPai());
}
$smarty->display('no_arvore.tpl');

/**
 * Função que mostra só os setores que o usuário tem permissão, caso ele não seja do tipo master
 * @param type $setor
 * @param type $tpl
 */
function showNode($setor, $tpl, $setores_permitidos, $usuario) {
    //$setor->descricao = AppController::detectaSaida($setor->descricao);
    $tem_filhos = $setor->filhos > 0 ? true : false;
    if ($usuario->getTipo() != Usuario::TIPO_MASTER) {
        if (in_array($setor->id, $setores_permitidos)) {
            $tpl->CLASS = $tem_filhos ? 'jstree-closed' : '';
            $tpl->S = $setor;
            $tpl->block("BLOCK_NO");
        } else if ($tem_filhos) {
            if ((new Setor())->temFilhoMarcado($setores_permitidos, $setor->id)) {
                $tpl->CLASS = 'jstree-closed jstree-open';
                $tpl->S = $setor;
                $tpl->block("BLOCK_NO");
            }
        }
    } else {
        $tpl->CLASS = $tem_filhos ? 'jstree-closed' : '';
        $tpl->DISABLED_NODE = 'disabled';
        $tpl->S = $setor;
        $tpl->block("BLOCK_NO");
    }
}
