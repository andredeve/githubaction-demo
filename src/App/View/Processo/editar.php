<?php

use App\Controller\ProcessoController;
use App\Enum\TipoHistoricoProcesso;
use App\Enum\TipoUsuario;
use App\Model\Assinatura;
use App\Model\Dao\ClassificacaoDao;
use App\Model\Dao\TipoAnexoDao;
use App\Model\Local;
use App\Model\Processo;
use App\Model\TipoLocal;
use App\Model\SubTipoLocal;
use Core\Controller\AppController;
use App\Controller\UsuarioController;
use App\Controller\AnexoController;
use App\Controller\IndexController;
use Core\Util\Functions;
use Doctrine\ORM\ORMException;

/**
 * @var $processo Processo
 */

$user = UsuarioController::getUsuarioLogadoDoctrine();
$hasAttachAddPermission = AnexoController::possuiPermissao($_REQUEST['objeto']);
$smarty->assign("hasAttachAddPermission", $hasAttachAddPermission);
$smarty->assign("pode_desarquivar", false);
$smarty->assign('page_icon', 'fa fa-edit');
$smarty->assign("acao", "atualizar");
$processo = $_REQUEST['objeto'];
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
$processo->contribuinteMarcarComoRecebido();
$usuarioEhInteressado = $user->isInteressado();
$processo->contribuinteMarcarComoRecebido();
$smarty->assign("processo", $processo);
$smarty->assign('usuarioEhInteressado', $usuarioEhInteressado);
$smarty->assign("abrirTramitacao", $usuarioEhInteressado && $processo->contribuintePodeAbrirTramitacao());
$smarty->assign("locais", (new Local())->listar());
$smarty->assign("tipos_local", (new TipoLocal())->listar());
$smarty->assign("subtipos_local", (new SubTipoLocal())->listar());
$smarty->assign("podeEditarSetorOrigem", $_REQUEST['podeEditarSetorOrigem']);
$smarty->assign("app_url", APP_URL);
$smarty->assign("usuario", $user);
$smarty->assign("tipos_documentos", (new TipoAnexoDao())->listarAtivos());
$smarty->assign("classificacoes", (new ClassificacaoDao())->listar());
$smarty->assign("qtdeTramites", count($processo->getTramites()));
$page_title = ($processo->getNumero() == null && $processo->getIsExterno())
    ? 'Recebimento de '.AppController::getParametosConfig('nomenclatura').' (Contribuinte)'
    : 'Editar '.AppController::getParametosConfig('nomenclatura');
$smarty->assign('page_title', $page_title);
$config = IndexController::getConfig();
if (isset($config['lxsign_url'])) {
    $smarty->assign('lxsign_url', $config['lxsign_url']);
    $smarty->assign('access_token', $config['access_token']);
}
if ($processo->getIsArquivado()) {
    try {
        $historico = $processo->getHistorico();
        $pode_desarquivar = false;
        foreach($historico as $log) {
            if ($log->getTipo() === TipoHistoricoProcesso::ARQUIVADO) {
                if ($user->isAdm() || ($log->getUsuario() != null && $log->getUsuario()->getId() === $user->getId())) {
                    $pode_desarquivar = true;
                    break;
                }
            }
        }
    } catch (ORMException $e) {
        Functions::escreverLogErro($e);
    }
    if (isset($pode_desarquivar) && $pode_desarquivar) {
        $smarty->assign("pode_desarquivar", true);
    } else {
        if ($user->isAdm()) {
            $smarty->assign("pode_desarquivar", true);
        } else {
            $smarty->assign("pode_desarquivar", false);
        }
    }
}
$smarty->assign("processo_n", "{$processo->getNumero()}/{$processo->getExercicio()}");
include VIEW_PATH . 'Processo/_assign.php';
if($usuario_logado->getTipo() != TipoUsuario::MASTER){
	$setores = $usuario_logado->getSetores();
	$adiciona = true;
	foreach ($setores as $setor){
    	if ($processo->getSetorOrigem()->getId() == $setor->getId()){
			$adiciona = false;
		}
	}
	if($adiciona){
		$setores->add($processo->getSetorOrigem());
	}
	$smarty->assign("setores", $setores);
}