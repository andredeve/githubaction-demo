<?php

use App\Model\StatusProcesso;
use App\Controller\AssinaturaController;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Tramite/Templates/';
$smarty->assign('app_url', APP_URL);
$processo = (new \App\Model\Processo())->buscar($_POST['processo_id']);
$assunto = isset($_POST['assunto_id']) && !empty($_POST['assunto_id']) ? (new \App\Model\Assunto())->buscar($_POST['assunto_id']) : $processo->getAssunto();
$numero_fase = $assunto != $processo->getAssunto() ? 1 : $processo->getNumeroFase(true);
$smarty->assign("setores_fase", $assunto->getFluxograma()->getFases($numero_fase)->getSetoresFase());
$smarty->assign('processo', $processo);
$smarty->assign('numero_fase', $numero_fase);
$smarty->assign('assunto', $assunto);
$smarty->assign('tramite', $processo->getTramiteAtual());
$smarty->assign('setor_origem', $processo->getTramiteAtual()->getSetorAtual());
$smarty->assign("status_processo", (new StatusProcesso())->listarAtivos());
$smarty->assign("status_inicial_id", 2);
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
if ($numero_fase != 1) {
    $smarty->assign("setores", (new \App\Model\Setor())->listarSetoresPai());
}
$smarty->display('destino.tpl');