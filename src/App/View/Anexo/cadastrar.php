<?php
use App\Enum\PermissaoStatus;
use App\Controller\UsuarioController;
use App\Model\Anexo;
use App\Model\Processo;
use App\Model\ModeloDocumento;
use Core\Controller\AppController;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';

$smarty->setTemplateDir(APP_PATH . '/src/App/View/Anexo/Templates/');
$usuario = UsuarioController::getUsuarioLogadoDoctrine();
$smarty->assign('app_url', APP_URL);
$smarty->assign("acao", "inserir");
$processo = !empty($_POST['processo_id']) ? (new Processo())->buscar($_POST['processo_id']) : unserialize($_SESSION['processo']);
$anexo = new Anexo();
$anexo->setProcesso($processo);
$permissao_alteracao = $anexo->podeSerAlterado();
$modelos = (new ModeloDocumento())->listar();
$anexo->setProcesso($processo);
$smarty->assign("modelos", $modelos);
$smarty->assign("processo_n", $processo->getNumero() . "/" . $processo->getExercicio());
$_SESSION['anexo'] = serialize($anexo);
$smarty->assign("anexo", $anexo);
$smarty->assign("usuarioEhInteressado", $usuario->isInteressado());
$smarty->assign("pode_editar", true);
$controller = new App\Controller\AssinaturaController();
$grupos = $controller->listarGruposAssinatura();
$smarty->assign("grupos", $grupos);
$smarty->assign("requer_motivo", $permissao_alteracao === PermissaoStatus::REQUER_MOTIVO);
$smarty->assign("file_version", AppController::getConfig("file_version"));
$smarty->assign("pode_editar", true);
include VIEW_PATH . 'Anexo/_assign.php';
$smarty->display('formulario.tpl');
