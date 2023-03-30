<?php

use App\Controller\UsuarioController;
use App\Enum\PermissaoStatus;
use App\Enum\TipoUsuario;
use App\Model\Anexo;
use App\Model\Assinatura;
use App\Model\ModeloDocumento;
use Core\Controller\AppController;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';

$smarty->template_dir = APP_PATH . '/src/App/View/Anexo/Templates/';
$smarty->assign('app_url', APP_URL);
$smarty->assign("acao", "atualizar");
$assinatura = null;
if (!empty($_POST['anexo_id'])) {
    $anexo = (new Anexo())->buscar($_POST['anexo_id']);
    $assinatura = Assinatura::buscarPorAnexo($anexo);
} else {
    $processo = unserialize($_SESSION['processo']);
    $anexo = $processo->getAnexos()->get($_POST['indice']);
}
$processo = $anexo->getProcesso();
$permissao_alteracao = $anexo->podeSerAlterado();
$tramite = $processo->getTramiteAtual();
$usuario = UsuarioController::getUsuarioLogadoDoctrine();
if ((!is_null($tramite) && !is_null($tramite->getUsuarioRecebimento())
        && $usuario->getId() === $tramite->getUsuarioRecebimento()->getId() 
        && $anexo->isRequired()) || $usuario->getTipo() === TipoUsuario::INTERESSADO) {
    $smarty->assign("pode_editar", PermissaoStatus::OK);
    $smarty->assign("requer_motivo", false);
} else {
    $smarty->assign("pode_editar", $permissao_alteracao !== PermissaoStatus::NEGADO);
    $smarty->assign("requer_motivo", $permissao_alteracao === PermissaoStatus::REQUER_MOTIVO);
}
$smarty->assign("processo_n", $processo->getNumero() . "/" . $processo->getExercicio());
$modelos = (new ModeloDocumento())->listar();
$_SESSION['anexo'] = serialize($anexo);
$smarty->assign("anexo", $anexo);
$smarty->assign("modelos", $modelos);
$smarty->assign("file_version", AppController::getConfig("file_version"));
include VIEW_PATH . 'Anexo/_assign.php';
$smarty->display('formulario.tpl');
