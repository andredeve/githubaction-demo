<?php

use App\Controller\ProcessoController;
use App\Enum\TipoUsuario;
use App\Controller\UsuarioController;
use App\Model\Processo;
use App\Model\Assinatura;
use Core\Controller\AppController;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';

$smarty->template_dir = APP_PATH . '/src/App/View/Anexo/Templates/';
$smarty->assign("hasAttachAddPermission", true);
$smarty->assign('app_url', APP_URL);
if (isset($_POST["processo_id"])) {
    $smarty->assign("acao", "atualizar");
} else {
    $smarty->assign("acao", "inserir");
}
$app_config = AppController::getConfig();
$smarty->assign('app', $app_config);
$config = AppController::getConfig();
if (isset($config['access_token'])) {
    $smarty->assign("lxsign_token", $config['access_token']);
}
if (isset($config['lxsign_url'])) {
    $smarty->assign("lxsign_url", $config['lxsign_url']);
}
if(!empty($_POST['processo_id'])) {
    $processo = (new Processo())->buscar($_POST['processo_id']);
} else {
    $processo = unserialize($_SESSION['processo']);
}
foreach ($processo->getAnexos() as &$anexo) {
    $anexo->setAssinatura(Assinatura::buscarPorAnexo($anexo));
}

$lxSignAnexosIds = $processo->buscarLxSignIdDosAnexos();
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
$smarty->assign('file_version', uniqid());
$smarty->assign("usuarioEhInteressado", UsuarioController::isInteressado());
$smarty->assign("processo", $processo);
$smarty->assign("usuario", UsuarioController::getUsuarioLogadoDoctrine());
$smarty->display('listar.tpl');
