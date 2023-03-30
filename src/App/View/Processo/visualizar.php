<?php

use App\Controller\ProcessoController;
use App\Enum\TipoHistoricoProcesso;
use App\Model\HistoricoProcesso;
use App\Model\Processo;
use App\Model\Assinatura;
use Core\Controller\AppController;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';

$smarty->template_dir = APP_PATH . '/src/App/View/Processo/Templates/';
$processo = (new Processo())->buscar($_POST['processo_id']);
$lxSignAnexosIds = $processo->buscarLxSignIdDosAnexos();
$usuario_logado = \App\Controller\UsuarioController::getUsuarioLogado();
$usuarioEhInteressado = $usuario_logado->isInteressado();
$smarty->assign("usuario_logado", $usuario_logado);
$smarty->assign('usuarioEhInteressado', $usuarioEhInteressado);
if (!empty($lxSignAnexosIds)) {
    $anexosStatusTemp = ProcessoController::buscarStatusAssinaturas($lxSignAnexosIds);
    foreach ($anexosStatusTemp as $status) {
        $anexosStatus[$status->id] = $status->status;
    }
}
foreach ($processo->getAnexos() as &$item) {
    $assinatura = Assinatura::buscarPorAnexo($item);
    $item->setAssinatura($assinatura);
    if (!is_null($assinatura) && isset($anexosStatus[$assinatura->getLxsign_id()])) {
        $item->status = $anexosStatus[$assinatura->getLxsign_id()];
    }
}
$smarty->assign('processo', $processo);
$smarty->assign('acao', 'visualizar');
$smarty->assign('app_url', APP_URL);
$autenticado = isset($_SESSION['processo_' . $processo->getId()]);
$smarty->assign('nomenclatura', \Core\Controller\AppController::getParametosConfig('nomenclatura'));
$config = AppController::getConfig();
if (isset($config['access_token'])) {
    $smarty->assign("lxsign_token", $config['access_token']);
}
if (isset($config['lxsign_url'])) {
    $smarty->assign("lxsign_url", $config['lxsign_url']);
}
if(!$processo->usuarioTemPermissaoProcesso()){
    $smarty->template_dir = APP_PATH . '/src/App/View/Public/Templates/';
    $smarty->display('sem_acesso_sigiloso.tpl');
    die();
}
if ($processo->getSigilo() == \App\Enum\SigiloProcesso::SIGILOSO && !$autenticado) {
    $smarty->assign('modal', true);
    $smarty->assign('page_title', \Core\Controller\AppController::getParametosConfig('nomenclatura') . ' Sigiloso');
    $smarty->assign('page_icon', 'fa fa-lock');
    $smarty->display('autenticar.tpl');
} else {
    HistoricoProcesso::registrar(TipoHistoricoProcesso::VISUALIZADO, $processo);
    $smarty->assign('acao', 'visualizar');
    $smarty->assign("locais", (new \App\Model\Local())->listar());
    $smarty->assign("tipos_local", (new \App\Model\TipoLocal())->listar());
    $smarty->assign("subtipos_local", (new \App\Model\SubTipoLocal())->listar());
    $smarty->display('visualizar.tpl');
}
