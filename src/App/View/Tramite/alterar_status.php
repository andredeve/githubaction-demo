<?php

use App\Model\StatusProcesso;
use App\Model\Tramite;

include '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Tramite/Templates/';
$tramite = new Tramite();
$tramite = $tramite->buscar($_POST['tramite_id']);
$smarty->assign("tramite", $tramite);
$smarty->assign("status_processo", (new StatusProcesso())->listarAtivos());
$smarty->assign('app_url', APP_URL);
$smarty->assign("locais", (new \App\Model\Local())->listar());
$smarty->assign("tipos_local", (new \App\Model\TipoLocal())->listar());
$smarty->assign("subtipos_local", (new \App\Model\SubTipoLocal())->listar());
$smarty->display('alterar_status.tpl');
