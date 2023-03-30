<?php

use App\Controller\AnexoController;
use App\Exception\LxSignException;
use App\Model\Anexo;
use App\Model\Assinatura;
use App\Model\Setor;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use Core\Util\Functions;
use Core\Util\Http\Client\Builder;
use Core\Util\Http\HTTP_METHOD;

if (!isset($smarty)) {
    $modal = true;
    include_once '../../../../bootstrap.php';
    include_once '../../../../_config/smarty.config.php';
    $config = AppController::getConfig();
    $smarty->setTemplateDir(APP_PATH . '/src/App/View/Assinatura/Templates/');
    $smarty->assign('app_url', APP_URL);
    $smarty->assign('lxsign_url', $config['lxsign_url']);
    $smarty->assign('access_token', "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjbnBqIjoiMDMuNDM0Ljc5MlwvMDAwMS0wOSJ9.zPJLb7eQwyXL3qbIQdcG_fZTjTEoNh1ha6h1P-t3vYw");
} else {
    $modal = false;
}
$assinaturaController = new App\Controller\AssinaturaController();
$grupos = $assinaturaController->listarGruposAssinatura();
$signatarios = $assinaturaController->listarSignatarios();
$tiposDocumentos = $assinaturaController->listarTiposDocumetos();
$empresas = $assinaturaController->listarEmpresas();
foreach ($grupos as $grupo) {    
    $grupo->signatariosIds = array_map(function($item) {
        return $item->id;
    }, $grupo->signatarios);
    $grupo->signatariosIds = json_encode($grupo->signatariosIds);
}
$config = AppController::getConfig();
$smarty->assign("signatarios", $signatarios);
$smarty->assign("grupos", $grupos);
$smarty->assign("tipos_documentos", $tiposDocumentos);
$smarty->assign("empresas", $empresas);
$smarty->assign("acao", "inserir");
$smarty->assign('anexo_id', $_REQUEST["anexo_id"]);
$smarty->assign('anexo_indice', $_REQUEST["anexo_indice"] ?? "");
$anexo = new Anexo();
if (isset($_REQUEST["anexo_id"]) && !empty($_REQUEST["anexo_id"])) {
    $anexo = $anexo->buscar($_REQUEST["anexo_id"]);
    $assinatura = Assinatura::buscarPorAnexo($anexo);
} else {
    $processo = unserialize($_SESSION['processo']);
    $smarty->assign('processo', $processo);
    $anexo = $processo->getAnexos()->get($_POST['anexo_indice']);
    $assinatura = $processo->getAnexos()->get($_POST['anexo_indice'])->getAssinatura()->get(0);
}
$extension = pathinfo($anexo->getArquivoPath(), PATHINFO_EXTENSION);
if (strtolower($extension) !== 'pdf') {
    http_response_code(400);
    AnexoController::setMessage(TipoMensagem::ERROR, "Permitido envio para assinatura apenas de arquivos  PDF.", null, true);
    die;
}
if (!is_null($assinatura)) {
    $tipoDocEmpresa = carregarTiposDeDocumentosEEmpresa($assinatura, $config);
    $smarty->assign('assinatura', $assinatura);
    if (!empty($tipoDocEmpresa->tipos_documentos)) {
        $smarty->assign('tipos_documentos', $tipoDocEmpresa->tipos_documentos);
    }
    if (!empty($tipoDocEmpresa->empresas)) {
        $smarty->assign('empresas', $tipoDocEmpresa->empresas);
    }
}
$smarty->assign('anexo', $anexo);
$smarty->assign('setores', (new Setor())->listar());
$smarty->assign('file_version', uniqid());
$stringNumero = $anexo->getNumero();
$arrayNumero = explode("/", $stringNumero);
$numero = $arrayNumero[0];
$exercicio = $arrayNumero[1] ?? date("Y");
$smarty->assign("consulta_assinatura", null);
if($assinatura){
    try {
        $consulta = $assinaturaController->consultarAssinatura($assinatura);
        if (!is_null($consulta)) {
            $smarty->assign("consulta_assinatura", $consulta);
        }
    } catch (LxSignException $e) {
        Functions::escreverLogErro($e);
    }
    $numero = $assinatura->getNumero() ? $assinatura->getNumero() : $numero;
    $exercicio = $assinatura->getExercicio() ? $assinatura->getExercicio() : $exercicio;
}
$smarty->assign("numero", $numero);
$smarty->assign("exercicio", $exercicio);

$smarty->assign('modal', $modal);
if ($modal) {
    $smarty->display('anexo.tpl');
}

function carregarTiposDeDocumentosEEmpresa(Assinatura $assinatura, array $sysConfig) {
    $params = $assinatura->getGrupoAsArray();
    $url = $sysConfig['lxsign_url'] . "GrupoSignatario/api/buscar-tipo-documentos";
    $response = (new Builder($url))
        ->setParameters(["access_token" => $sysConfig['access_token']])
        ->setBody(['grupos_id' => $params->toArray()])
        ->setMethod(HTTP_METHOD::POST)
        ->verifySSL(false)
        ->build()
        ->send();
    return $response->getBody()->toObject();
}