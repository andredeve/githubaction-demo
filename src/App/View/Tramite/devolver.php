<?php
include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';

use App\Controller\IndexController;
use App\Model\Setor;
use App\Model\StatusProcesso;
use App\Model\Tramite;
if (isset($_POST['tramite_id_sel']) && !empty($_POST['tramite_id_sel'])){
    if(count($_POST['tramite_id_sel'])> 1){
        die('Não é permitido recusar mais de um processo por vez');
    } else {
        $_POST['tramite_id'] = $_POST['tramite_id_sel'][0];
    }
}

$tramite = (new Tramite())->buscar($_POST['tramite_id']);
$smarty->template_dir = APP_PATH . '/src/App/View/Tramite/Templates/';
$smarty->assign('app_url', APP_URL);
$smarty->assign('tramite', $tramite);
$smarty->assign("setores", (new Setor())->listarSetoresPai());
$smarty->assign("status_processo", (new StatusProcesso())->listarAtivos());
$smarty->assign("nomenclatura", IndexController::getParametosConfig()['nomenclatura']);
$usuarioEnvio = $tramite->getUsuarioEnvio();
if (!is_null($usuarioEnvio) && $usuarioEnvio->getTipo() === \App\Enum\TipoUsuario::INTERESSADO) {
    $smarty->assign("pode_assinar", false);
} else {
    $smarty->assign("pode_assinar", true);
}
$smarty->display('devolver.tpl');