<?php

use App\Controller\UsuarioController;
use App\Enum\TipoPessoa;
use App\Enum\TipoUsuario;
use App\Model\Interessado;
use App\Model\Pessoa;
use Core\Controller\AppController;

if (!isset($smarty)) {
    $modal = true;
    include_once '../../../../bootstrap.php';
    include_once '../../../../_config/smarty.config.php';
    $smarty->template_dir = APP_PATH . '/src/App/View/Interessado/Templates/';
    $smarty->assign('app_url', APP_URL);
} else {
    $modal = false;
}

$pessoa = new Pessoa();
$pessoa->setTipo(TipoPessoa::FISICA);
$interessado = new Interessado();


//$pessoa->setNome("VICTOR");
//$pessoa->setCpf("22222222222");
//$pessoa->setRg("222222222");
//$pessoa->setSexo("m");
//$pessoa->setEmail("victor@lxtec.com.br");
//$pessoa->setCelular("(22) 22222-2222");
//$pessoa->setTelefone("(22) 2222-2222");
//$pessoa->setDataNascimento(new DateTime());
//$pessoa->setNacionalidade("lx121314");

$interessado->setPessoa($pessoa);
$smarty->assign('page_title', 'Novo Interessado');
$smarty->assign('page_icon', 'fa fa-user-plus');
$smarty->assign('page_description', 'cadastre um novo interessado');
$smarty->assign("acao", "inserir");

$smarty->assign('file_version', AppController::getConfig()['file_version']);
$smarty->assign("interessado", $interessado);
include VIEW_PATH . 'Interessado/_assign.php';
$smarty->assign('modal', $modal);
if ($modal) {
    $smarty->display('formulario.tpl');
}