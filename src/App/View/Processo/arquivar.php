<?php

use App\Controller\UsuarioController;
use App\Enum\TipoUsuario;
use App\Model\Grupo;
use App\Model\Local;
use App\Model\Processo;
use App\Model\SubTipoLocal;
use App\Model\TipoLocal;

include '../../../../bootstrap.php';
$usuario_logado = UsuarioController::getUsuarioLogadoDoctrine();
$grupo = $usuario_logado->getGrupo();
if ($grupo->getArquivar() || $usuario_logado->getTipo() == TipoUsuario::MASTER) {
    include_once '../../../../_config/smarty.config.php';
    $smarty->template_dir = APP_PATH . '/src/App/View/Processo/Templates/';
    $processo = new Processo();
    $processo = $processo->buscar($_POST['processo_id']);
    $smarty->assign("processo", $processo);
    $smarty->assign("locais", (new Local())->listar());
    $smarty->assign("tipos_local", (new TipoLocal())->listar());
    $smarty->assign("subtipos_local", (new SubTipoLocal())->listar());
    $smarty->assign('app_url', APP_URL);
    $smarty->display('arquivar.tpl');
} else {
    echo Grupo::createNoPermissisionError();
}
