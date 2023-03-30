<?php

use App\Controller\AssinaturaController;
use App\Controller\IndexController;
use App\Controller\UsuarioController;
use App\Enum\TipoUsuario;
use App\Model\Assunto;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Tramite/Templates/';
$assunto_id = $_POST['assunto_id'];
$numero_fase = $_POST['numero_fase'];
$setor_id = $_POST['setor_id'];
$assunto = new Assunto();
$assunto = $assunto->buscar($assunto_id);
$smarty->assign('assunto', $assunto);
$smarty->assign('numero_fase', $numero_fase);
$smarty->assign('setor_id', $setor_id);
$smarty->assign("data_atual", Date('d/m/Y'));
$assinaturaController = new AssinaturaController();
$grupos = $assinaturaController->listarGruposAssinatura();
foreach ($grupos as $grupo) {
    $grupo->signatarios = array_map(function($item) {
        return $item->id;
    }, $grupo->signatarios);
    $grupo->signatarios = json_encode($grupo->signatarios);
}
$signatarios = $assinaturaController->listarSignatarios();
$tiposDocumentos = $assinaturaController->listarTiposDocumetos();
$empresas = $assinaturaController->listarEmpresas();
$smarty->assign("grupos", $grupos);
$smarty->assign("signatarios", $signatarios);
$smarty->assign("tipos_documentos", $tiposDocumentos);
$smarty->assign("empresas", $empresas);
$parametros = IndexController::getParametosConfig();
$smarty->assign('usuarioEhInteressado', UsuarioController::isInteressado());
$smarty->assign('parametros', $parametros);
$smarty->assign('app_url', APP_URL);
$smarty->assign('file_version', uniqid() );
$smarty->display('requisitos.tpl');
