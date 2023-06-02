<?php
include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';

use App\Controller\UsuarioController;
use App\Enum\TipoUsuario;
use App\Model\Assunto;
use App\Model\Grupo;
use App\Model\Processo;
use App\Model\Setor;
use App\Model\StatusProcesso;
use App\Controller\IndexController;
use App\Model\Tramite;
use Doctrine\Common\Collections\ArrayCollection;
use Core\Controller\AppController;

$usuario_logado = UsuarioController::getUsuarioLogadoDoctrine();
$grupo = $usuario_logado->getGrupo();

if ($grupo->getTramitar() || in_array($usuario_logado->getTipo(), array(TipoUsuario::MASTER, TipoUsuario::INTERESSADO))) {
    $smarty->setTemplateDir(APP_PATH . '/src/App/View/Tramite/Templates/');
    if (isset($_POST['tramite_id']) || (isset($_POST['tramite_id_sel']) && count($_POST['tramite_id_sel']) == 1)) {
        //Trâmite Comum
        $tramite = (new Tramite())->buscar(isset($_POST['tramite_id_sel']) ? $_POST['tramite_id_sel'][0] : $_POST['tramite_id']);
        $processo = $tramite->getProcesso();
        if(isset($_POST['tramites']) && !empty($_POST['tramites']) && $_POST['tramites'] < count($processo->getTramites()) && $usuario_logado->getTipo() != TipoUsuario::INTERESSADO)
            die('Esse processo já foi tramitado. Por favor, atualize a página.');
        $smarty->assign('assunto', $processo->getAssunto());
        $smarty->assign('temSetores', $processo->getAssunto()->getSetores($usuario_logado));
        $smarty->assign('setor_origem', $tramite->getSetorAtual());
        $smarty->assign('processo', $processo);
        $smarty->assign('tramite', $tramite);
        $smarty->assign('cancelar', $_POST['cancelar'] ?? 0);
        $smarty->assign("numero_fase", !$tramite->getForaFluxograma() || $tramite->getCancelouDecisao() ? $processo->getNumeroFase(true) + 1 : $processo->getNumeroFase(true));
        $smarty->assign('devolver', $tramite->getForaFluxograma() && !$tramite->getCancelouDecisao() ? 1 : 0);
        $smarty->assign('setor_id', null);
        $smarty->assign('origem_unica', true);
        $smarty->assign('form', true);
    } elseif (isset($_POST['tramite_id_sel'])) {
        //Tramitar Massa
        $tramites = new ArrayCollection();
        $setores_origem = array();
        foreach ($_POST['tramite_id_sel'] as $tramite_id) {
            $tramite = (new Tramite())->buscar($tramite_id);
            if (empty($tramite->getRequirimentosObrigaroriosNaoCumpridos()) && !$tramite->temFluxograma() && !$tramites->contains($tramite)) {
                $setores_origem[] = $tramite->getSetorAtual();
                $tramites->add($tramite);
            }
        }
        if ($tramites->count() > 0) {
            $smarty->assign('origem_unica', count(array_unique($setores_origem)) == 1);
            $smarty->assign('setor_origem', null);
            $smarty->assign('tramites', $tramites);
            $smarty->assign('form', true);
        } else {
            $smarty->assign("sem_tramites", true);
        }
        $smarty->assign('disableRequimentoDeDocumentacao', false);
        if( count($_POST['tramite_id_sel']) > 1){
            $smarty->assign('disableRequimentoDeDocumentacao', true);
        }
        $smarty->assign('devolver', 0);
    } else {
        $processo = isset($_SESSION['processo']) && !empty($_SESSION['processo'])? unserialize($_SESSION['processo']):  new Processo();
        //Primeiro trâmite
        $smarty->assign('processo', $processo);
        $smarty->assign('form', false);
        $smarty->assign('assunto', (new Assunto())->buscar($_POST['assunto_id']));
        $smarty->assign('setor_origem', (new Setor())->buscar($_POST['setor_origem_id']));
        $smarty->assign('origem_unica', true);
        $smarty->assign('devolver', 0);
        $smarty->assign("numero_fase", 1);
        $smarty->assign('primeiro_tramite', true);
    }
    $smarty->assign("setores", (new \App\Model\Setor())->listarSetoresPai());
    $smarty->assign("setor_selecionado", null);
    $smarty->assign("status_processo", (new StatusProcesso())->listarAtivos());
    $smarty->assign('app_url', APP_URL);
    $smarty->assign("data_atual", Date('d/m/Y'));
    $smarty->assign("status_inicial_id", 2);
    $smarty->assign('file_version', uniqid() );
    $smarty->assign('usuarioIsExterno', $usuario_logado->getTipo() === TipoUsuario::INTERESSADO);
    $smarty->assign('tramite_inicial_contribuinte', ($_POST['tramite_inicial_contribuinte'] ?? false));
    $config = AppController::getConfig();
    if (isset($config['lxsign_url'])) {
        $smarty->assign('lxsign_url', $config['lxsign_url'] . "GrupoSignatario/api/buscar-tipo-documentos?access_token={$config['access_token']}");
        $assinaturaController = new \App\Controller\AssinaturaController();
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
    }
    $parametros = IndexController::getParametosConfig();
    $smarty->assign('parametros', $parametros);
    $smarty->display('tramitar.tpl');
} else {
    echo Grupo::createNoPermissisionError();
}