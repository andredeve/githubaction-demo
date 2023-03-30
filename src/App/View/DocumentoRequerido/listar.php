<?php
use App\Model\Processo;
use App\Model\Assinatura;
use Core\Controller\AppController;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';

$smarty->template_dir = APP_PATH . '/src/App/View/DocumentoRequerido/Templates/';
$smarty->assign('app_url', APP_URL);
$app_config = \Core\Controller\AppController::getConfig();
$smarty->assign('app', $app_config);
$config = AppController::getConfig();




$tramite = new \App\Model\Tramite();
$tramiteCadastro = $tramite->buscar($_POST['tramite_cadastro_id']);

$smarty->assign("tramiteCadastro", $tramiteCadastro);
$smarty->display('listar.tpl');
