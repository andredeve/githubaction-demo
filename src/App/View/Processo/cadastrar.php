<?php

use App\Controller\IndexController;
use App\Enum\OrigemProcesso;
use App\Enum\SigiloProcesso;
use App\Model\Dao\ClassificacaoDao;
use App\Model\Dao\SetorDao;
use App\Model\Dao\TipoAnexoDao;
use App\Model\Processo;
use App\Model\Setor;
use Core\Controller\AppController;
use App\Controller\UsuarioController;


$processo = new Processo();
$processo->setDataAbertura(new DateTime());
$parametros = AppController::getParametosConfig();
$page_title = 'Novo ' . AppController::getParametosConfig('nomenclatura');
$usuarioEhInteressado = UsuarioController::isInteressado();

if(isset($parametros["origem_processo"]) && !empty($parametros["origem_processo"]) && !$usuarioEhInteressado){
    $processo->setOrigem($parametros["origem_processo"]);
}
if (!empty($parametros["setor_origem_processo"])) {
    $setor_origem = (new SetorDao())->buscarPorDescricao($parametros["setor_origem_processo"]);
    if (!empty($setor_origem) && $setor_origem[0] instanceof Setor) {
        $processo->setSetorOrigem($setor_origem[0]);
    }
}
if($usuarioEhInteressado){
    $page_title = 'Cadastro de Abertura de '. AppController::getParametosConfig('nomenclatura');
    $processo->setOrigem(OrigemProcesso::EXTERNA);
    $processo->setIsExterno(true);
    $processo->setSigilo(SigiloProcesso::SEM_RESTRICAO);
    $processo->setSetorOrigem((new Setor())->buscar($parametros['processo_setor_contribuinte_id']));
}
$smarty->assign('usuarioEhInteressado', $usuarioEhInteressado);
$smarty->assign("file_version", uniqid());
$smarty->assign("hasAttachAddPermission", true);
$smarty->assign('page_title', $page_title);
$smarty->assign('page_icon', 'fa fa-plus');
$smarty->assign("acao", "inserir");
$processo->setNumeroFase(0);
$smarty->assign("processo", $processo);
$smarty->assign('obrigatoriaCI', false);
$smarty->assign('data_atual', Date('d/m/Y'));
$smarty->assign("pode_desarquivar", false);
$smarty->assign("tipos_documentos", (new TipoAnexoDao())->listarAtivos());
$smarty->assign("classificacoes", (new ClassificacaoDao())->listar());
$_SESSION['processo'] = serialize($processo);
$config = IndexController::getConfig();
if (isset($config['lxsign_url'])) {
    $smarty->assign('lxsign_url', $config['lxsign_url']);
    $smarty->assign('access_token', $config['access_token']);
}
include VIEW_PATH . 'Processo/_assign.php';