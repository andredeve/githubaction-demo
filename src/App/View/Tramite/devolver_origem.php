<?php

use App\Model\StatusProcesso;
use App\Model\Tramite;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Tramite/Templates/';
$smarty->assign('app_url', APP_URL);
if (isset($_POST['tramite_id_sel']) && !empty($_POST['tramite_id_sel'])){
    if(count($_POST['tramite_id_sel'])> 1){
        die('Não é permitido recusar mais de um processo por vez');
    } else {
        $_POST['tramite_id'] = $_POST['tramite_id_sel'][0];
    }
}

$tramite = (new Tramite())->buscar($_POST['tramite_id']);
$smarty->assign('tramite', $tramite);
$smarty->assign("setores", (new \App\Model\Setor())->listarSetoresPai());
$smarty->assign("status_processo", (new StatusProcesso())->listarAtivos());
$smarty->assign("nomenclatura",\App\Controller\IndexController::getParametosConfig()['nomenclatura']);
$smarty->display('devolver_origem.tpl');