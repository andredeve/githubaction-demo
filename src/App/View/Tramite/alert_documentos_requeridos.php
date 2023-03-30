<?php

use App\Model\StatusProcesso;
use App\Model\Tramite;

include '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Tramite/Templates/';
$tramite = new Tramite();
$tramite = $tramite->buscar($_POST['tramite_id']);
$smarty->assign("tramite", $tramite);
$smarty->display('alert_documentos_requeridos.tpl');
