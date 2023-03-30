<?php
use App\Controller\UsuarioController;
use App\Enum\TipoUsuario;
use Core\Controller\AppController;

$app_config = AppController::getConfig();
$smarty->assign('cliente_config', AppController::getClienteConfig());
$smarty->assign('app_url', APP_URL);
$smarty->assign('app', $app_config);
$processo = $_REQUEST['processo'];
$smarty->assign('processo', $processo );
if(!$processo->consultaPublicaTemAcessoAoProcesso()){
    $smarty->template_dir = APP_PATH . '/src/App/View/Public/Templates/';
    $smarty->display('sem_acesso_sigiloso.tpl');
    die();
}
$smarty->assign("nomenclatura", AppController::getParametosConfig()['nomenclatura']);
$smarty->assign('anos', (new \App\Model\Processo())->getExercicios());
$smarty->assign("usuarioEhInteressado", UsuarioController::isInteressado());